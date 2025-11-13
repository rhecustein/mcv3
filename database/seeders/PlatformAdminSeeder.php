<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\PlatformAdmin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class PlatformAdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Super Admin
        $superAdmin = User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@mcv3.local',
            'password' => Hash::make('password'),
            'role_type' => 'superadmin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        PlatformAdmin::create([
            'user_id' => $superAdmin->id,
            'role' => 'super_admin',
            'is_active' => true,
        ]);

        // Create Admin
        $admin = User::create([
            'name' => 'Admin MCv3',
            'email' => 'admin@mcv3.local',
            'password' => Hash::make('password'),
            'role_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        PlatformAdmin::create([
            'user_id' => $admin->id,
            'role' => 'admin',
            'permissions' => ['manage_tenants', 'view_analytics', 'manage_users'],
            'is_active' => true,
        ]);

        // Create Billing Manager
        $billing = User::create([
            'name' => 'Billing Manager',
            'email' => 'billing@mcv3.local',
            'password' => Hash::make('password'),
            'role_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        PlatformAdmin::create([
            'user_id' => $billing->id,
            'role' => 'billing',
            'permissions' => ['manage_billing', 'manage_subscriptions', 'view_analytics'],
            'is_active' => true,
        ]);

        // Create Support Staff
        $support = User::create([
            'name' => 'Support Staff',
            'email' => 'support@mcv3.local',
            'password' => Hash::make('password'),
            'role_type' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        PlatformAdmin::create([
            'user_id' => $support->id,
            'role' => 'support',
            'permissions' => ['view_analytics', 'send_notifications'],
            'is_active' => true,
        ]);

        $this->command->info('Platform admins created successfully!');
        $this->command->info('Super Admin: superadmin@mcv3.local / password');
        $this->command->info('Admin: admin@mcv3.local / password');
        $this->command->info('Billing: billing@mcv3.local / password');
        $this->command->info('Support: support@mcv3.local / password');
    }
}
