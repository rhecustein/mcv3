<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TenantDashboardController;
use App\Http\Controllers\McuMarketplaceController;

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

// MCU Marketplace
Route::prefix('mcu')->name('mcu.')->group(function () {
    // Marketplace
    Route::get('/', [McuMarketplaceController::class, 'index'])->name('marketplace.index');
    Route::get('/packages/{package}', [McuMarketplaceController::class, 'showPackage'])->name('marketplace.package');
    Route::get('/providers/{provider}', [McuMarketplaceController::class, 'showProvider'])->name('marketplace.provider');

    // Bookings
    Route::prefix('bookings')->name('bookings.')->group(function () {
        Route::get('/', [McuMarketplaceController::class, 'myBookings'])->name('index');
        Route::get('/create/{package}', [McuMarketplaceController::class, 'showBookingForm'])->name('create');
        Route::post('/create/{package}', [McuMarketplaceController::class, 'storeBooking'])->name('store');
        Route::get('/{booking}', [McuMarketplaceController::class, 'showBooking'])->name('show');
        Route::post('/{booking}/cancel', [McuMarketplaceController::class, 'cancelBooking'])->name('cancel');
    });
});

// All existing routes should be included here
// For now, we'll use the existing web routes as tenant routes
// Later, we can move specific routes here

// Include existing application routes (temporary - will be refactored)
require __DIR__.'/web.php';
