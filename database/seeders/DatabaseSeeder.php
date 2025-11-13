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
            // Platform Admin (Sprint 2)
            PlatformAdminSeeder::class,

            // Multi-Tenancy & Subscription (Sprint 1)
            SubscriptionPlanSeeder::class,
            TenantSeeder::class,

            // MCU Marketplace (Sprint 3)
            McuSeeder::class,

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
