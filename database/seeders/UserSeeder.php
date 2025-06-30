<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['superadmin', 'admin'];

        foreach ($roles as $role) {
            $email = $role . '@suratsehat.co.id';

            // Gunakan DB langsung agar tidak terpengaruh mutator model
            DB::table('users')->updateOrInsert(
                ['email' => $email],
                [
                    'name' => ucfirst($role) . ' User',
                    'password' => Hash::make('password123'), // ✅ benar-benar 1x bcrypt
                    'role_type' => $role,
                    'phone' => '081234567890',
                    'avatar' => null,
                    'last_ip' => '127.0.0.1',
                    'last_login_at' => now(),
                    'email_verified_at' => now(),
                    'otp_code' => null,
                    'otp_code_expired_at' => null,
                    'remember_token' => Str::random(10),
                    'updated_at' => now(),
                    'created_at' => now(), // aman untuk insert baru
                ]
            );

            echo "✅ User $email seeded.\n";
        }
    }
}
