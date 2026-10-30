<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function clients(): Response
    {
        return Inertia::render('Catalog', [
            'section' => 'clients',
            'records' => Client::query()
                ->withCount(['orders', 'projects', 'invoices'])
                ->latest('id')
                ->get(),
        ]);
    }

    public function services(): Response
    {
        return Inertia::render('Catalog', [
            'section' => 'services',
            'records' => Service::query()
                ->withCount(['orderItems', 'invoiceItems'])
                ->latest('id')
                ->get(),
        ]);
    }
}
