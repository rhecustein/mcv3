<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        // Buat user akun untuk admin
        $user = User::create([
            'name' => 'Admin Jakarta Selatan',
            'email' => 'admin.jaksel@suratsehat.test',
            'password' => Hash::make('password'),
            'role_type' => 'admin',
            'phone' => '081234567891',
            'email_verified_at' => now(),
            'last_login_at' => now(),
        ]);

        // Buat data admin terhubung ke user
        Admin::create([
            'user_id' => $user->id,
            'region_name' => 'Jakarta Selatan',
            'province' => 'DKI Jakarta',
            'position_title' => 'Regional Health Admin',
            'contact_number' => '021-12345678',
        ]);
    }
}
