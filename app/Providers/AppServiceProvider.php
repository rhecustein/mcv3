<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // ✅ Gate untuk tiap role_type (tanpa spatie)
        Gate::define('isSuperadmin', fn ($user) => $user->role_type === 'superadmin');
        Gate::define('isAdmin', fn ($user) => $user->role_type === 'admin');
        Gate::define('isOutlet', fn ($user) => $user->role_type === 'outlet');
        Gate::define('isDoctor', fn ($user) => $user->role_type === 'doctor');
        Gate::define('isCompanyAdmin', fn ($user) => $user->role_type === 'companies');
        Gate::define('isPatient', fn ($user) => $user->role_type === 'patient');
    }
}
