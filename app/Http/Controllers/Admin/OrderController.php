<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $orders = Order::query()
            ->with(['client:id,code,raison_sociale'])
            ->withCount('items')
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference', 'like', "%{$search}%")
                        ->orWhereHas('client', function ($client) use ($search) {
                            $client->where('raison_sociale', 'like', "%{$search}%")
                                ->orWhere('code', 'like', "%{$search}%");
                        });
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->query('status'));
            })
            ->latest('id')
            ->paginate(min(max((int) $request->query('per_page', 15), 1), 100))
            ->withQueryString();

        return response()->json($orders);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $order = DB::transaction(function () use ($data) {
            $this->checkClient($data['client_id']);

            // La référence provisoire reste dans la transaction.
            $order = Order::create([
                'client_id' => $data['client_id'],
                'reference' => 'TMP-'.(string) Str::uuid(),
                'order_date' => $data['order_date'],
                'status' => 'draft',
                'currency' => 'MAD',
                'notes' => $data['notes'] ?? null,
                'total_ht' => 0,
                'total_tva' => 0,
                'total_ttc' => 0,
            ]);

            $order->update([
                'reference' => 'CMD-'.str_pad(
                    (string) $order->id,
                    6,
                    '0',
                    STR_PAD_LEFT
                ),
            ]);

            $this->saveItems($order, $data['items']);

            $order->update([
                'status' => $data['status'] ?? 'draft',
            ]);

            return $order->load(['client', 'items.service']);
        });

        return response()->json([
            'message' => 'Commande créée avec succès.',
            'order' => $order,
        ], 201);
    }

    public function show(Order $order): JsonResponse
    {
        return response()->json(
            $order->load(['client', 'items.service', 'invoices'])
        );
    }

    public function update(Request $request, Order $order): JsonResponse
    {
        $data = $this->validatedData($request);

        $order = DB::transaction(function () use ($order, $data) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($order->status !== 'draft') {
                throw ValidationException::withMessages([
                    'status' => 'Seules les commandes brouillon peuvent être modifiées.',
                ]);
            }

            if ($order->invoices()->exists()) {
                throw ValidationException::withMessages([
                    'order' => 'Cette commande possède déjà une facture.',
                ]);
            }

            $this->checkClient($data['client_id']);

            $order->update([
                'client_id' => $data['client_id'],
                'order_date' => $data['order_date'],
                'notes' => $data['notes'] ?? null,
            ]);

            $order->items()->delete();

            $this->saveItems($order, $data['items']);

            $order->update([
                'status' => $data['status'] ?? 'draft',
            ]);

            return $order->load(['client', 'items.service']);
        });

        return response()->json([
            'message' => 'Commande modifiée avec succès.',
            'order' => $order,
        ]);
    }

    public function destroy(Order $order): JsonResponse
    {
        DB::transaction(function () use ($order) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($order->id);

            if ($order->status !== 'draft' || $order->invoices()->exists()) {
                throw ValidationException::withMessages([
                    'order' => 'Seule une commande brouillon sans facture peut être supprimée.',
                ]);
            }

            $order->items()->delete();
            $order->delete();
        });

        return response()->json([
            'message' => 'Commande supprimée avec succès.',
        ]);
    }

    public function validateOrder(Order $order): JsonResponse
    {
        $order = DB::transaction(function () use ($order): Order {
            $order = Order::query()->lockForUpdate()->findOrFail($order->id);

            if ($order->status !== 'draft') {
                throw ValidationException::withMessages([
                    'order' => 'Seul un devis brouillon peut devenir une commande.',
                ]);
            }

            $order->update(['status' => 'validated']);

            return $order->fresh(['client', 'items.service']);
        });

        return response()->json([
            'message' => 'Le devis est devenu une commande.',
            'order' => $order,
        ]);
    }

    private function checkClient(int $clientId): void
    {
        if (! Client::whereKey($clientId)
            ->where('status', 'active')
            ->exists()) {
            throw ValidationException::withMessages([
                'client_id' => 'Le client doit être actif.',
            ]);
        }
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'client_id' => [
                'required',
                'integer',
                Rule::exists('clients', 'id')->whereNull('deleted_at'),
            ],
            'order_date' => ['required', 'date_format:Y-m-d'],
            'status' => [
                'sometimes',
                Rule::in(['draft', 'validated']),
            ],
            'notes' => ['nullable', 'string'],

            'items' => ['required', 'array', 'min:1'],
            'items.*.service_id' => [
                'required',
                'integer',
                Rule::exists('services', 'id')->whereNull('deleted_at'),
            ],
            'items.*.description' => ['nullable', 'string', 'max:255'],
            'items.*.start_date' => ['nullable', 'date_format:Y-m-d'],
            'items.*.end_date' => [
                'nullable',
                'date_format:Y-m-d',
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'gt:0',
                'max:999.99',
                'decimal:0,2',
            ],
            'items.*.unit_price_ht' => [
                'required',
                'numeric',
                'min:0',
                'max:99999999.99',
                'decimal:0,2',
            ],
            'items.*.tva_rate' => [
                'required',
                'numeric',
                'between:0,100',
                'decimal:0,2',
            ],
        ]);
    }

    private function saveItems(Order $order, array $items): void
    {
        $totalHt = 0;
        $totalTva = 0;

        foreach ($items as $index => $item) {
            $service = Service::query()
                ->whereKey($item['service_id'])
                ->where('is_active', true)
                ->first();

            if (! $service) {
                throw ValidationException::withMessages([
                    "items.$index.service_id" => 'Service inexistant ou inactif.',
                ]);
            }

            $start = $item['start_date'] ?? null;
            $end = $item['end_date'] ?? null;

            if ($start && $end && $end < $start) {
                throw ValidationException::withMessages([
                    "items.$index.end_date" => 'La date de fin doit être postérieure ou égale à la date de début.',
                ]);
            }

            // Calculs monétaires en centimes entiers.
            $quantity = $this->scaledInt($item['quantity']);
            $unitPrice = $this->scaledInt($item['unit_price_ht']);
            $vatRate = $this->scaledInt($item['tva_rate']);

            // quantity / 100 * unitPrice en centimes.
            $amountHt = intdiv($quantity * $unitPrice + 50, 100);

            // TVA exprimée en centièmes de pourcentage.
            $amountTva = intdiv($amountHt * $vatRate + 5000, 10000);
            $amountTtc = $amountHt + $amountTva;

            $order->items()->create([
                'service_id' => $service->id,
                'description' => $item['description'] ?? $service->name,
                'start_date' => $start,
                'end_date' => $end,
                'quantity' => $this->money($quantity),
                'unit_price_ht' => $this->money($unitPrice),
                'tva_rate' => $this->money($vatRate),
                'amount_ht' => $this->money($amountHt),
                'amount_tva' => $this->money($amountTva),
                'amount_ttc' => $this->money($amountTtc),
            ]);

            $totalHt += $amountHt;
            $totalTva += $amountTva;
        }

        $order->update([
            'total_ht' => $this->money($totalHt),
            'total_tva' => $this->money($totalTva),
            'total_ttc' => $this->money($totalHt + $totalTva),
        ]);
    }

    private function scaledInt(string|int|float $value): int
    {
        $parts = explode('.', (string) $value, 2);

        $whole = (int) $parts[0];
        $fraction = (int) str_pad(
            substr($parts[1] ?? '', 0, 2),
            2,
            '0'
        );

        return $whole * 100 + $fraction;
    }

    private function money(int $cents): string
    {
        return sprintf(
            '%d.%02d',
            intdiv($cents, 100),
            $cents % 100
        );
    }
}
