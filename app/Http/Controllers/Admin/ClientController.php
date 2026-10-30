<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = trim((string) $request->query('search', ''));

        $clients = Client::query()
            ->withCount(['orders', 'projects', 'invoices'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('raison_sociale', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%")
                        ->orWhere('ice', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->latest('id')
            ->paginate(min(max((int) $request->query('per_page', 15), 1), 100))
            ->withQueryString();

        return response()->json($clients);
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->validatedData($request);

        $client = Client::create($data);

        return response()->json([
            'message' => 'Client créé avec succès.',
            'client' => $client,
        ], 201);
    }

    public function show(Client $client): JsonResponse
    {
        $client->loadCount(['orders', 'projects', 'invoices']);

        return response()->json($client);
    }

    public function update(Request $request, Client $client): JsonResponse
    {
        $data = $this->validatedData($request, $client);

        $client->update($data);

        return response()->json([
            'message' => 'Client modifié avec succès.',
            'client' => $client->fresh(),
        ]);
    }

    public function destroy(Client $client): JsonResponse
    {
        if (
            $client->orders()->exists() ||
            $client->projects()->exists() ||
            $client->invoices()->exists()
        ) {
            return response()->json([
                'message' => 'Suppression impossible : ce client possède des documents ou projets.',
            ], 409);
        }

        $client->delete();

        return response()->json([
            'message' => 'Client supprimé avec succès.',
        ]);
    }

    private function validatedData(Request $request, ?Client $client = null): array
    {
        return $request->validate([
            'type' => ['required', Rule::in(['company', 'individual'])],
            'code' => [
                'required',
                'string',
                'max:30',
                Rule::unique('clients', 'code')->ignore($client?->id),
            ],
            'raison_sociale' => ['required', 'string', 'max:255'],
            'ice' => ['nullable', 'string', 'max:30'],
            'identifiant_fiscal' => ['nullable', 'string', 'max:30'],
            'rc' => ['nullable', 'string', 'max:50'],
            'taxe_professionnelle' => ['nullable', 'string', 'max:50'],
            'contact_name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'telephone' => ['nullable', 'string', 'max:30'],
            'website' => ['nullable', 'url', 'max:255'],
            'adresse' => ['nullable', 'string'],
            'ville' => ['nullable', 'string', 'max:255'],
            'pays' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
