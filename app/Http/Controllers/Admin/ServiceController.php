<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $services = Service::query()
            ->withCount(['orderItems', 'invoiceItems'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when($request->filled('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->latest('id')
            ->paginate(min(max((int) $request->query('per_page', 15), 1), 100))
            ->withQueryString();

        return response()->json($services);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $service = Service::create($data);

        return response()->json([
            'message' => 'Service créé avec succès.',
            'service' => $service,
        ], 201);
    }

    public function show(Service $service): JsonResponse
    {
        return response()->json(
            $service->loadCount(['orderItems', 'invoiceItems'])
        );
    }

    public function update(Request $request, Service $service): JsonResponse
    {
        $data = $this->validatedData($request, $service);

        $service->update($data);

        return response()->json([
            'message' => 'Service modifié avec succès.',
            'service' => $service->fresh(),
        ]);
    }

    public function destroy(Service $service): JsonResponse
    {
        if (
            $service->orderItems()->exists() ||
            $service->invoiceItems()->exists()
        ) {
            return response()->json([
                'message' => 'Ce service est utilisé dans une commande ou facture. Désactivez-le plutôt.',
            ], 409);
        }

        $service->delete();

        return response()->json([
            'message' => 'Service supprimé avec succès.',
        ]);
    }

    private function validatedData(Request $request, ?Service $service = null): array
    {
        return $request->validate([
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('services', 'code')->ignore($service?->id),
            ],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'billing_type' => [
                'required',
                Rule::in(['one_time', 'monthly', 'quarterly', 'yearly']),
            ],
            'default_price_ht' => ['required', 'numeric', 'min:0', 'max:9999999999999.99'],
            'default_tva_rate' => ['required', 'numeric', 'between:0,100'],
            'currency' => ['required', 'string', 'size:3'],
            'is_active' => ['required', 'boolean'],
        ]);
    }
}
