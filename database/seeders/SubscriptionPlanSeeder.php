<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'description' => 'Perfect for small clinics just getting started with digital health certificates',
                'price_monthly' => 500000, // Rp 500K
                'price_yearly' => 5000000, // Rp 5M (save 1M/year)
                'currency' => 'IDR',
                'max_users' => 5,
                'max_documents_per_month' => 500,
                'max_storage_mb' => 2000, // 2 GB
                'max_api_calls_per_day' => 1000,
                'features' => [
                    'basic_medical_certificates',
                    'digital_signatures',
                    'qr_verification',
                    'email_notifications',
                    'basic_analytics',
                    'pdf_optimization',
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'description' => 'For growing clinics with MCU marketplace and advanced features',
                'price_monthly' => 2000000, // Rp 2M
                'price_yearly' => 20000000, // Rp 20M (save 4M/year)
                'currency' => 'IDR',
                'max_users' => 25,
                'max_documents_per_month' => 5000,
                'max_storage_mb' => 20000, // 20 GB
                'max_api_calls_per_day' => 10000,
                'features' => [
                    'all_starter_features',
                    'mcu_marketplace',
                    'mcu_workflow_digitalization',
                    'comprehensive_reports',
                    'whatsapp_notifications',
                    'api_access',
                    'custom_domain',
                    'advanced_analytics',
                    'marketing_automation',
                    'priority_support',
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'description' => 'For large hospitals and enterprise-level healthcare organizations',
                'price_monthly' => 5000000, // Rp 5M (custom pricing)
                'price_yearly' => 50000000, // Rp 50M (custom pricing)
                'currency' => 'IDR',
                'max_users' => 999999, // Unlimited
                'max_documents_per_month' => 999999, // Unlimited
                'max_storage_mb' => 999999, // Unlimited
                'max_api_calls_per_day' => 999999, // Unlimited
                'features' => [
                    'all_professional_features',
                    'b2b_corporate_portal',
                    'employee_health_management',
                    'hris_integration',
                    'lis_pacs_integration',
                    'white_label',
                    'custom_branding',
                    'dedicated_account_manager',
                    'sla_guarantee',
                    'custom_development',
                    'on_premise_option',
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            SubscriptionPlan::create($plan);
        }

        $this->command->info('✅ Subscription plans seeded successfully!');
    }
}
