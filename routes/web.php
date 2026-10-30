
<?php

use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\CatalogController;
use App\Http\Controllers\CommercialDocumentController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| GORAKMANA - Site Public
|--------------------------------------------------------------------------
*/

Route::inertia('/', 'Welcome')->name('home');

/*
|--------------------------------------------------------------------------
| GORAKMANA - Dashboard
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'quotes'])
        ->name('dashboard');
    Route::get('/commercial/devis', [DashboardController::class, 'quotes'])
        ->name('commercial.quotes');
    Route::get('/commercial/commandes', [DashboardController::class, 'orders'])
        ->name('commercial.orders');
    Route::get('/commercial/factures', [DashboardController::class, 'invoices'])
        ->name('commercial.invoices');
    Route::get('/catalogue/clients', [CatalogController::class, 'clients'])
        ->name('catalog.clients');
    Route::get('/catalogue/services', [CatalogController::class, 'services'])
        ->name('catalog.services');
    Route::get('/documents/devis/{order}.pdf', [CommercialDocumentController::class, 'quote'])
        ->name('documents.quotes.pdf');
    Route::get('/documents/commandes/{order}.pdf', [CommercialDocumentController::class, 'order'])
        ->name('documents.orders.pdf');
    Route::get('/documents/factures/{invoice}.pdf', [CommercialDocumentController::class, 'invoice'])
        ->name('documents.invoices.pdf');

});

/*
|--------------------------------------------------------------------------
| GORAKMANA - Administration
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        // Gestion des clients
        Route::apiResource('clients', ClientController::class);

        // Gestion des services
        Route::apiResource('services', ServiceController::class);

        // Gestion des commandes
        Route::apiResource('orders', OrderController::class);

        Route::post('orders/{order}/validate', [OrderController::class, 'validateOrder'])
            ->name('orders.validate');

        Route::post('orders/{order}/invoice', [InvoiceController::class, 'store'])
            ->name('orders.invoice.store');
        Route::get('invoices/{invoice}', [InvoiceController::class, 'show'])
            ->name('invoices.show');

    });

/*
|--------------------------------------------------------------------------
| Paramètres utilisateur
|--------------------------------------------------------------------------
*/

require __DIR__.'/settings.php';
