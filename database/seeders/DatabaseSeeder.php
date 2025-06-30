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
