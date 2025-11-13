<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Platform\AdminDashboardController;
use App\Http\Controllers\Platform\BillingController;

/*
|--------------------------------------------------------------------------
| Platform Admin Routes
|--------------------------------------------------------------------------
|
| These routes are for platform administrators to manage the entire system,
| including all tenants, billing, analytics, and system settings.
|
*/

Route::prefix('platform')->name('platform.')->middleware(['auth'])->group(function () {

    // Admin Dashboard
    Route::get('/admin', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    Route::get('/admin/analytics', [AdminDashboardController::class, 'analytics'])->name('admin.analytics');

    // Tenant Management
    Route::prefix('admin/tenants')->name('admin.tenants.')->group(function () {
        Route::get('/', [AdminDashboardController::class, 'tenants'])->name('index');
        Route::get('/create', [AdminDashboardController::class, 'createTenant'])->name('create');
        Route::post('/', [AdminDashboardController::class, 'storeTenant'])->name('store');
        Route::get('/{tenant}', [AdminDashboardController::class, 'showTenant'])->name('show');
        Route::get('/{tenant}/edit', [AdminDashboardController::class, 'editTenant'])->name('edit');
        Route::put('/{tenant}', [AdminDashboardController::class, 'updateTenant'])->name('update');
        Route::post('/{tenant}/suspend', [AdminDashboardController::class, 'suspendTenant'])->name('suspend');
        Route::post('/{tenant}/reactivate', [AdminDashboardController::class, 'reactivateTenant'])->name('reactivate');
        Route::delete('/{tenant}', [AdminDashboardController::class, 'destroyTenant'])->name('destroy');
    });

    // Billing Management
    Route::prefix('billing')->name('billing.')->group(function () {
        Route::get('/', [BillingController::class, 'index'])->name('index');
        Route::get('/invoices', [BillingController::class, 'invoices'])->name('invoices');
        Route::get('/invoices/{invoice}', [BillingController::class, 'showInvoice'])->name('invoices.show');
        Route::post('/invoices/generate', [BillingController::class, 'generateInvoices'])->name('invoices.generate');
        Route::post('/invoices/{invoice}/send', [BillingController::class, 'sendInvoice'])->name('invoices.send');
        Route::post('/invoices/{invoice}/mark-paid', [BillingController::class, 'markAsPaid'])->name('invoices.mark-paid');
        Route::post('/invoices/mark-overdue', [BillingController::class, 'markOverdueInvoices'])->name('invoices.mark-overdue');
        Route::post('/invoices/{invoice}/cancel', [BillingController::class, 'cancelInvoice'])->name('invoices.cancel');
    });
});
