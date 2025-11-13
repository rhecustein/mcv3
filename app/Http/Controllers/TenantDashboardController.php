<?php

namespace App\Http\Controllers;

use App\Models\Tenant;
use App\Models\TenantUsage;
use App\Models\TenantInvoice;
use Illuminate\Http\Request;

class TenantDashboardController extends Controller
{
    /**
     * Display tenant dashboard with subscription info and usage stats.
     */
    public function index(Request $request)
    {
        $tenant = app('tenant');

        // Get current month usage
        $currentUsage = TenantUsage::where('tenant_id', $tenant->id)
            ->currentMonth()
            ->first();

        // Calculate usage percentages
        $usageStats = [
            'users' => [
                'current' => $tenant->current_users,
                'max' => $tenant->max_users,
                'percentage' => $tenant->getUsagePercentage('users'),
                'remaining' => $tenant->max_users - $tenant->current_users,
            ],
            'documents' => [
                'current' => $tenant->current_documents_this_month,
                'max' => $tenant->max_documents_per_month,
                'percentage' => $tenant->getUsagePercentage('documents'),
                'remaining' => $tenant->getRemainingDocuments(),
            ],
            'storage' => [
                'current' => $tenant->current_storage_mb,
                'max' => $tenant->max_storage_mb,
                'percentage' => $tenant->getUsagePercentage('storage'),
                'remaining' => $tenant->max_storage_mb - $tenant->current_storage_mb,
            ],
        ];

        // Get subscription info
        $subscriptionInfo = [
            'plan' => $tenant->subscription_plan,
            'status' => $tenant->subscription_status,
            'is_trial' => $tenant->isTrialing(),
            'trial_days_remaining' => $tenant->trialDaysRemaining(),
            'ends_at' => $tenant->subscription_ends_at,
            'enabled_features' => $tenant->enabled_features ?? [],
        ];

        // Get recent invoices
        $recentInvoices = TenantInvoice::where('tenant_id', $tenant->id)
            ->orderBy('invoice_date', 'desc')
            ->limit(5)
            ->get();

        return view('tenant.dashboard', compact(
            'tenant',
            'usageStats',
            'subscriptionInfo',
            'currentUsage',
            'recentInvoices'
        ));
    }

    /**
     * Display tenant settings page.
     */
    public function settings(Request $request)
    {
        $tenant = app('tenant');

        return view('tenant.settings.index', compact('tenant'));
    }

    /**
     * Display subscription management page.
     */
    public function subscription(Request $request)
    {
        $tenant = app('tenant');

        // Get current subscription
        $currentSubscription = $tenant->subscriptions()
            ->where('status', 'active')
            ->latest()
            ->first();

        // Get available plans
        $availablePlans = \App\Models\SubscriptionPlan::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return view('tenant.settings.subscription', compact(
            'tenant',
            'currentSubscription',
            'availablePlans'
        ));
    }

    /**
     * Display usage statistics page.
     */
    public function usage(Request $request)
    {
        $tenant = app('tenant');

        // Get last 6 months usage data
        $usageHistory = TenantUsage::where('tenant_id', $tenant->id)
            ->where('period_start', '>=', now()->subMonths(6)->startOfMonth())
            ->orderBy('period_start', 'desc')
            ->get();

        // Current month usage
        $currentUsage = TenantUsage::where('tenant_id', $tenant->id)
            ->currentMonth()
            ->first();

        return view('tenant.settings.usage', compact(
            'tenant',
            'usageHistory',
            'currentUsage'
        ));
    }

    /**
     * Display billing and invoices page.
     */
    public function billing(Request $request)
    {
        $tenant = app('tenant');

        // Get all invoices
        $invoices = TenantInvoice::where('tenant_id', $tenant->id)
            ->orderBy('invoice_date', 'desc')
            ->paginate(20);

        // Get payment summary
        $paymentSummary = [
            'total_paid' => TenantInvoice::where('tenant_id', $tenant->id)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'total_outstanding' => TenantInvoice::where('tenant_id', $tenant->id)
                ->whereIn('status', ['sent', 'overdue'])
                ->sum('total_amount'),
            'overdue_count' => TenantInvoice::where('tenant_id', $tenant->id)
                ->where('status', 'overdue')
                ->count(),
        ];

        return view('tenant.settings.billing', compact(
            'tenant',
            'invoices',
            'paymentSummary'
        ));
    }
}
