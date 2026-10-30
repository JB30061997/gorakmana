<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\Service;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function quotes(): Response
    {
        return $this->renderCommercialPage('quotes');
    }

    public function orders(): Response
    {
        return $this->renderCommercialPage('orders');
    }

    public function invoices(): Response
    {
        return $this->renderCommercialPage('invoices');
    }

    private function renderCommercialPage(string $section): Response
    {
        return Inertia::render('Dashboard', [
            'section' => $section,
            'stats' => [
                'clients' => Client::where('status', 'active')->count(),
                'quotes' => Order::count(),
                'invoices' => Invoice::count(),
                'revenue' => Invoice::whereIn('status', ['issued', 'partially_paid', 'paid'])->sum('total_ttc'),
            ],
            'clients' => Client::query()
                ->where('status', 'active')
                ->orderBy('raison_sociale')
                ->get(['id', 'code', 'raison_sociale']),
            'services' => Service::query()
                ->where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name', 'default_price_ht', 'default_tva_rate']),
            'quotes' => Order::query()
                ->with(['client:id,raison_sociale', 'items.service:id,name', 'invoices:id,order_id,invoice_number,status'])
                ->where('status', 'draft')
                ->latest('id')
                ->limit(30)
                ->get(),
            'orders' => Order::query()
                ->with(['client:id,raison_sociale', 'items.service:id,name', 'invoices:id,order_id,invoice_number,status'])
                ->whereIn('status', ['validated', 'in_progress', 'completed'])
                ->latest('id')
                ->limit(30)
                ->get(),
            'invoices' => Invoice::query()
                ->with(['client:id,raison_sociale', 'items'])
                ->latest('id')
                ->limit(30)
                ->get(),
        ]);
    }
}
