<?php

namespace App\Providers;

use App\Models\Company;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Result;
use App\Policies\CompanyPolicy;
use App\Policies\DoctorPolicy;
use App\Policies\PatientPolicy;
use App\Policies\ResultPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Result::class => ResultPolicy::class,
        Patient::class => PatientPolicy::class,
        Doctor::class => DoctorPolicy::class,
        Company::class => CompanyPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
