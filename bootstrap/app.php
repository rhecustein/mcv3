<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Route;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        then: function () {
            // Platform Admin routes (main domain)
            Route::middleware(['web'])
                ->group(base_path('routes/platform.php'));

            // Tenant routes (subdomain-based)
            Route::middleware(['web', \App\Http\Middleware\TenantAware::class])
                ->domain('{tenant}.' . parse_url(config('app.url'), PHP_URL_HOST))
                ->group(base_path('routes/tenant.php'));
        }
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->append(\App\Http\Middleware\AutoLogoutIfInactive::class);
        // TenantAware middleware will be applied via route groups
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->withCommands([
        \App\Console\Commands\LogoutIdleSessions::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\EnsureSingleSession::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        $schedule->command('sessions:logout-idle')->everyFiveMinutes();
    })
    ->create();
