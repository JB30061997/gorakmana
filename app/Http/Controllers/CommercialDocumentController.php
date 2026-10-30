<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class CommercialDocumentController extends Controller
{
    public function quote(Order $order): Response
    {
        return $this->downloadOrder($order, 'DEVIS');
    }

    public function order(Order $order): Response
    {
        return $this->downloadOrder($order, 'COMMANDE');
    }

    public function invoice(Invoice $invoice): Response
    {
        $invoice->loadMissing(['client', 'items.service']);

        return $this->download([
            'documentTitle' => 'FACTURE',
            'documentNumber' => $invoice->invoice_number,
            'documentDate' => $invoice->invoice_date,
            'dueDate' => $invoice->due_date,
            'recipient' => (object) [
                'name' => $invoice->client_name,
                'address' => $invoice->client_address,
                'email' => $invoice->client->email,
                'phone' => $invoice->client->telephone,
            ],
            'rows' => $this->invoiceRows($invoice),
            'summary' => [
                'total_ht' => $invoice->total_ht,
                'vat_amount' => $invoice->total_tva,
                'total_ttc' => $invoice->total_ttc,
            ],
            'notice' => 'La présente facture reprend les prestations détaillées ci-dessus. Tous les montants sont exprimés en dirhams marocains (MAD).',
        ]);
    }

    private function downloadOrder(Order $order, string $documentTitle): Response
    {
        $order->loadMissing(['client', 'items.service']);
        $documentNumber = $documentTitle === 'DEVIS'
            ? Str::replaceStart('CMD-', 'DEV-', $order->reference)
            : $order->reference;

        return $this->download([
            'documentTitle' => $documentTitle,
            'documentNumber' => $documentNumber,
            'documentDate' => $order->order_date,
            'dueDate' => null,
            'recipient' => (object) [
                'name' => $order->client->raison_sociale,
                'address' => collect([$order->client->adresse, $order->client->ville, $order->client->pays])->filter()->join(', '),
                'email' => $order->client->email,
                'phone' => $order->client->telephone,
            ],
            'rows' => $this->orderRows($order),
            'summary' => [
                'total_ht' => $order->total_ht,
                'vat_amount' => $order->total_tva,
                'total_ttc' => $order->total_ttc,
            ],
            'notice' => $documentTitle === 'DEVIS'
                ? 'Ce devis est valable 30 jours à compter de sa date d’émission. Tous les montants sont exprimés en dirhams marocains (MAD).'
                : 'La présente commande confirme les prestations détaillées ci-dessus. Tous les montants sont exprimés en dirhams marocains (MAD).',
        ]);
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function download(array $data): Response
    {
        $logo = file_get_contents(public_path('images/gorakmana/logo.png'));
        $data['logoDataUri'] = 'data:image/png;base64,'.base64_encode((string) $logo);
        $data['generatedAt'] = now();

        return Pdf::loadView('pdf.commercial-document', $data)
            ->setPaper('a4')
            ->download(Str::slug($data['documentTitle'].'-'.$data['documentNumber']).'.pdf');
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function orderRows(Order $order): Collection
    {
        return $order->items->map(fn ($item): array => [
            'date' => $order->order_date->format('d/m/Y'),
            'reference' => $item->service?->code ?? '—',
            'service' => $item->service?->name ?? $item->description,
            'route' => $item->description,
            'quantity' => $item->quantity,
            'unit_price_ht' => $item->unit_price_ht,
            'vat_rate' => $item->tva_rate,
            'amount_ttc' => $item->amount_ttc,
        ]);
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    private function invoiceRows(Invoice $invoice): Collection
    {
        return $invoice->items->map(fn ($item): array => [
            'date' => $invoice->invoice_date->format('d/m/Y'),
            'reference' => $item->service?->code ?? '—',
            'service' => $item->service?->name ?? $item->description,
            'route' => $item->description,
            'quantity' => $item->quantity,
            'unit_price_ht' => $item->unit_price_ht,
            'vat_rate' => $item->tva_rate,
            'amount_ttc' => $item->amount_ttc,
        ]);
    }
}
