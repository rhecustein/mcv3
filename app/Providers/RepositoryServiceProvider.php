<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Repository Service Provider
 * Binds repository interfaces to their concrete implementations
 */
class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Bind Result Repository
        $this->app->bind(
            \App\Repositories\Contracts\ResultRepositoryInterface::class,
            \App\Repositories\Eloquent\ResultRepository::class
        );

        // Bind Patient Repository
        $this->app->bind(
            \App\Repositories\Contracts\PatientRepositoryInterface::class,
            \App\Repositories\Eloquent\PatientRepository::class
        );

        // Bind Doctor Repository
        $this->app->bind(
            \App\Repositories\Contracts\DoctorRepositoryInterface::class,
            \App\Repositories\Eloquent\DoctorRepository::class
        );
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
