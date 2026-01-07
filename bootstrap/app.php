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
        \App\Console\Commands\CompressPdfsCommand::class,
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->append(\App\Http\Middleware\EnsureSingleSession::class);
    })
    ->withSchedule(function (Schedule $schedule): void {
        // Session management
        $schedule->command('sessions:logout-idle')->everyFiveMinutes();

        // PDF Management - Compression (runs daily at 1:00 AM)
        $schedule->call(function () {
            $settings = \App\Models\PdfStorageSetting::globalDefaults();

            if ($settings->compression_enabled) {
                \App\Jobs\Maintenance\CompressPdfsJob::dispatch(
                    daysOld: $settings->compression_days,
                    tenantId: null,
                    quality: null, // Auto-determine based on age
                    limit: 1000
                );
            }
        })->dailyAt('01:00')->name('compress-pdfs');

        // PDF Management - Cleanup (runs daily at 2:00 AM, after compression)
        $schedule->call(function () {
            $settings = \App\Models\PdfStorageSetting::globalDefaults();

            if ($settings->auto_delete_enabled) {
                \App\Jobs\Maintenance\CleanupOldPdfsJob::dispatch(
                    daysOld: $settings->auto_delete_days,
                    archive: $settings->archive_before_delete,
                    tenantId: null
                );
            }
        })->dailyAt('02:00')->name('cleanup-old-pdfs');
    })
    ->create();
