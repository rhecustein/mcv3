<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantDashboardController;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| These routes are loaded by bootstrap/app.php and are applied to all
| requests with a subdomain (e.g., kimiafarma.mcv3.local).
|
| The TenantAware middleware automatically resolves the tenant and makes
| it available via app('tenant') or $request->tenant.
|
*/

// Tenant dashboard - shows subscription info, usage stats, quick actions
Route::get('/', [TenantDashboardController::class, 'index'])->name('tenant.dashboard');

// Tenant settings and subscription management
Route::prefix('settings')->name('tenant.settings.')->group(function () {
    Route::get('/', [TenantDashboardController::class, 'settings'])->name('index');
    Route::get('/subscription', [TenantDashboardController::class, 'subscription'])->name('subscription');
    Route::get('/usage', [TenantDashboardController::class, 'usage'])->name('usage');
    Route::get('/billing', [TenantDashboardController::class, 'billing'])->name('billing');
});

// All existing routes should be included here
// For now, we'll use the existing web routes as tenant routes
// Later, we can move specific routes here

// Include existing application routes (temporary - will be refactored)
require __DIR__.'/web.php';
