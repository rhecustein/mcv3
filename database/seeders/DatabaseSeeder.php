<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Multi-Tenancy & Subscription (NEW - Sprint 1)
            SubscriptionPlanSeeder::class,
            TenantSeeder::class,

            // Existing Seeders
            UserSeeder::class,
            AdminSeeder::class,
            OutletSeeder::class,
            DoctorSeeder::class,
            PackageSeeder::class,
            CompanyAdminSeeder::class,
            PatientSeeder::class,
            TemplateResultSeeder::class,
            IcdMasterSeeder::class,
        ]);
    }
}
