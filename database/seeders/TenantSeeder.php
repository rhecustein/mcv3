<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\SubscriptionPlan;
use App\Models\TenantSubscription;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get subscription plans
        $starterPlan = SubscriptionPlan::where('slug', 'starter')->first();
        $proPlan = SubscriptionPlan::where('slug', 'professional')->first();

        // Create demo tenants
        $tenants = [
            [
                'name' => 'Klinik Kimia Farma Sehat',
                'slug' => 'kimiafarma',
                'domain' => null,
                'subscription_plan' => 'professional',
                'subscription_status' => 'active',
                'trial_ends_at' => null,
                'subscription_ends_at' => now()->addYear(),
                'max_users' => 25,
                'max_documents_per_month' => 5000,
                'max_storage_mb' => 20000,
                'current_users' => 3,
                'current_documents_this_month' => 150,
                'current_storage_mb' => 450.50,
                'contact_email' => 'admin@kimiafarma.co.id',
                'contact_phone' => '021-12345678',
                'is_active' => true,
                'enabled_features' => [
                    'mcu_marketplace',
                    'whatsapp_notifications',
                    'api_access',
                ],
                'plan' => $proPlan,
            ],
            [
                'name' => 'RS Siloam Jakarta',
                'slug' => 'siloam',
                'domain' => null,
                'subscription_plan' => 'starter',
                'subscription_status' => 'trial',
                'trial_ends_at' => now()->addDays(10),
                'subscription_ends_at' => null,
                'max_users' => 5,
                'max_documents_per_month' => 500,
                'max_storage_mb' => 2000,
                'current_users' => 2,
                'current_documents_this_month' => 45,
                'current_storage_mb' => 120.25,
                'contact_email' => 'admin@siloam.com',
                'contact_phone' => '021-87654321',
                'is_active' => true,
                'enabled_features' => [
                    'basic_medical_certificates',
                    'qr_verification',
                ],
                'plan' => $starterPlan,
            ],
            [
                'name' => 'Klinik Pratama Sehat Sentosa',
                'slug' => 'pratama',
                'domain' => null,
                'subscription_plan' => 'professional',
                'subscription_status' => 'active',
                'trial_ends_at' => null,
                'subscription_ends_at' => now()->addMonths(6),
                'max_users' => 25,
                'max_documents_per_month' => 5000,
                'max_storage_mb' => 20000,
                'current_users' => 8,
                'current_documents_this_month' => 320,
                'current_storage_mb' => 890.75,
                'contact_email' => 'info@pratama.com',
                'contact_phone' => '021-55555555',
                'is_active' => true,
                'enabled_features' => [
                    'mcu_marketplace',
                    'whatsapp_notifications',
                ],
                'plan' => $proPlan,
            ],
        ];

        foreach ($tenants as $tenantData) {
            $plan = $tenantData['plan'];
            unset($tenantData['plan']);

            $tenant = Tenant::create($tenantData);

            // Create subscription for active tenants
            if ($tenant->subscription_status === 'active') {
                TenantSubscription::create([
                    'tenant_id' => $tenant->id,
                    'subscription_plan_id' => $plan->id,
                    'starts_at' => now(),
                    'ends_at' => $tenant->subscription_ends_at,
                    'billing_cycle' => 'monthly',
                    'price' => $plan->price_monthly,
                    'currency' => 'IDR',
                    'status' => 'active',
                    'auto_renew' => true,
                    'payment_method' => 'bank_transfer',
                    'paid_at' => now(),
                    'is_trial' => false,
                ]);
            }

            $this->command->info("✅ Created tenant: {$tenant->name} ({$tenant->slug})");
        }

        $this->command->info('');
        $this->command->info('🎉 All demo tenants created successfully!');
        $this->command->info('');
        $this->command->info('Access tenants:');
        $this->command->info('- http://kimiafarma.mcv3.local');
        $this->command->info('- http://siloam.mcv3.local');
        $this->command->info('- http://pratama.mcv3.local');
    }
}
