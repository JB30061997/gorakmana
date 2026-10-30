<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class InvoiceController extends Controller
{
    public function store(Order $order): JsonResponse
    {
        $invoice = DB::transaction(function () use ($order): Invoice {
            $order = Order::query()
                ->with(['client', 'items'])
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($order->status === 'cancelled') {
                throw ValidationException::withMessages([
                    'order' => 'Un devis annulé ne peut pas être facturé.',
                ]);
            }

            if ($order->invoices()->exists()) {
                throw ValidationException::withMessages([
                    'order' => 'Une facture existe déjà pour ce devis.',
                ]);
            }

            $invoice = Invoice::create([
                'client_id' => $order->client_id,
                'order_id' => $order->id,
                'invoice_number' => 'TMP-'.(string) str()->uuid(),
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->addDays(30)->toDateString(),
                'client_name' => $order->client->raison_sociale,
                'client_ice' => $order->client->ice,
                'client_if' => $order->client->identifiant_fiscal,
                'client_address' => collect([$order->client->adresse, $order->client->ville, $order->client->pays])
                    ->filter()
                    ->join(', '),
                'status' => 'issued',
                'total_ht' => $order->total_ht,
                'total_tva' => $order->total_tva,
                'total_ttc' => $order->total_ttc,
                'currency' => $order->currency,
                'notes' => $order->notes,
            ]);

            $invoice->update([
                'invoice_number' => 'FAC-'.now()->format('Y').'-'.str_pad((string) $invoice->id, 6, '0', STR_PAD_LEFT),
            ]);

            foreach ($order->items as $item) {
                $invoice->items()->create($item->only([
                    'service_id', 'description', 'start_date', 'end_date', 'quantity',
                    'unit_price_ht', 'tva_rate', 'amount_ht', 'amount_tva', 'amount_ttc',
                ]));
            }

            if ($order->status === 'draft') {
                $order->update(['status' => 'validated']);
            }

            return $invoice->load(['client', 'items']);
        });

        return response()->json([
            'message' => 'Facture générée avec succès.',
            'invoice' => $invoice,
        ], 201);
    }

    public function show(Invoice $invoice): JsonResponse
    {
        return response()->json($invoice->load(['client', 'items', 'payments']));
    }
}
