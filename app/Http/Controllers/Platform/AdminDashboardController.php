<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantUsage;
use App\Models\TenantInvoice;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    /**
     * Display platform admin dashboard with overview statistics
     */
    public function index()
    {
        // Overall statistics
        $stats = [
            'total_tenants' => Tenant::count(),
            'active_tenants' => Tenant::where('is_active', true)->count(),
            'trial_tenants' => Tenant::where('subscription_status', 'trial')->count(),
            'suspended_tenants' => Tenant::where('subscription_status', 'suspended')->count(),
            'total_users' => User::count(),
            'total_revenue_this_month' => TenantInvoice::whereMonth('invoice_date', now()->month)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'pending_invoices' => TenantInvoice::whereIn('status', ['sent', 'overdue'])->count(),
            'overdue_invoices' => TenantInvoice::where('status', 'overdue')->count(),
        ];

        // Recent tenants
        $recentTenants = Tenant::with('activeSub scription')
            ->latest()
            ->limit(10)
            ->get();

        // Subscription distribution
        $subscriptionDistribution = Tenant::select('subscription_plan', DB::raw('count(*) as count'))
            ->groupBy('subscription_plan')
            ->get()
            ->pluck('count', 'subscription_plan');

        // Revenue trend (last 6 months)
        $revenueTrend = TenantInvoice::select(
                DB::raw('DATE_FORMAT(invoice_date, "%Y-%m") as month'),
                DB::raw('SUM(total_amount) as revenue')
            )
            ->where('status', 'paid')
            ->where('invoice_date', '>=', now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Tenants expiring soon (within 7 days)
        $expiringTenants = Tenant::where('subscription_ends_at', '<=', now()->addDays(7))
            ->where('subscription_ends_at', '>', now())
            ->where('subscription_status', 'active')
            ->limit(10)
            ->get();

        return view('platform.admin.dashboard', compact(
            'stats',
            'recentTenants',
            'subscriptionDistribution',
            'revenueTrend',
            'expiringTenants'
        ));
    }

    /**
     * Display system-wide analytics
     */
    public function analytics()
    {
        // Usage statistics across all tenants
        $usageStats = [
            'total_documents_this_month' => TenantUsage::currentMonth()->sum('documents_generated'),
            'total_mcu_bookings_this_month' => TenantUsage::currentMonth()->sum('mcu_bookings'),
            'total_storage_used' => Tenant::sum('current_storage_mb'),
            'total_api_calls_this_month' => TenantUsage::currentMonth()->sum('api_calls'),
        ];

        // Top tenants by usage
        $topTenantsByDocuments = Tenant::select('tenants.*', 'tenant_usage.documents_generated')
            ->join('tenant_usage', 'tenants.id', '=', 'tenant_usage.tenant_id')
            ->whereMonth('tenant_usage.period_start', now()->month)
            ->orderBy('tenant_usage.documents_generated', 'desc')
            ->limit(10)
            ->get();

        // Growth metrics
        $growthMetrics = [
            'new_tenants_this_month' => Tenant::whereMonth('created_at', now()->month)->count(),
            'new_tenants_last_month' => Tenant::whereMonth('created_at', now()->subMonth()->month)->count(),
            'churned_tenants_this_month' => Tenant::where('subscription_status', 'cancelled')
                ->whereMonth('updated_at', now()->month)
                ->count(),
        ];

        return view('platform.admin.analytics', compact(
            'usageStats',
            'topTenantsByDocuments',
            'growthMetrics'
        ));
    }

    /**
     * Display all tenants with filtering
     */
    public function tenants(Request $request)
    {
        $query = Tenant::with('activeSubscription');

        // Filters
        if ($request->filled('status')) {
            $query->where('subscription_status', $request->status);
        }

        if ($request->filled('plan')) {
            $query->where('subscription_plan', $request->plan);
        }

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('slug', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        $tenants = $query->latest()->paginate(20);

        return view('platform.admin.tenants.index', compact('tenants'));
    }

    /**
     * Show tenant details
     */
    public function showTenant(Tenant $tenant)
    {
        $tenant->load(['subscriptions', 'invoices', 'usage']);

        // Current month usage
        $currentUsage = TenantUsage::where('tenant_id', $tenant->id)
            ->currentMonth()
            ->first();

        // Usage history (6 months)
        $usageHistory = TenantUsage::where('tenant_id', $tenant->id)
            ->where('period_start', '>=', now()->subMonths(6)->startOfMonth())
            ->orderBy('period_start', 'desc')
            ->get();

        // Recent invoices
        $recentInvoices = TenantInvoice::where('tenant_id', $tenant->id)
            ->latest()
            ->limit(10)
            ->get();

        return view('platform.admin.tenants.show', compact(
            'tenant',
            'currentUsage',
            'usageHistory',
            'recentInvoices'
        ));
    }

    /**
     * Show create tenant form
     */
    public function createTenant()
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();
        return view('platform.admin.tenants.create', compact('plans'));
    }

    /**
     * Store new tenant
     */
    public function storeTenant(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:50|unique:tenants,slug|alpha_dash',
            'email' => 'required|email|unique:tenants,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'subscription_plan' => 'required|in:starter,professional,enterprise',
            'subscription_status' => 'required|in:trial,active',
            'trial_days' => 'nullable|integer|min:0|max:90',
        ]);

        // Get plan details
        $plan = \App\Models\SubscriptionPlan::where('slug', $validated['subscription_plan'])->first();

        $tenant = Tenant::create([
            'name' => $validated['name'],
            'slug' => $validated['slug'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'address' => $validated['address'] ?? null,
            'subscription_plan' => $validated['subscription_plan'],
            'subscription_status' => $validated['subscription_status'],
            'is_active' => true,
            'max_users' => $plan->max_users,
            'max_documents_per_month' => $plan->max_documents_per_month,
            'max_storage_mb' => $plan->max_storage_mb,
            'trial_ends_at' => $validated['subscription_status'] === 'trial'
                ? now()->addDays($validated['trial_days'] ?? 14)
                : null,
            'subscription_starts_at' => now(),
            'subscription_ends_at' => now()->addYear(),
        ]);

        return redirect()
            ->route('platform.admin.tenants.show', $tenant)
            ->with('success', 'Tenant created successfully!');
    }

    /**
     * Show edit tenant form
     */
    public function editTenant(Tenant $tenant)
    {
        $plans = \App\Models\SubscriptionPlan::where('is_active', true)->get();
        return view('platform.admin.tenants.edit', compact('tenant', 'plans'));
    }

    /**
     * Update tenant
     */
    public function updateTenant(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:tenants,email,' . $tenant->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'is_active' => 'required|boolean',
            'subscription_status' => 'required|in:trial,active,suspended,cancelled',
        ]);

        $tenant->update($validated);

        return redirect()
            ->route('platform.admin.tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully!');
    }

    /**
     * Suspend tenant
     */
    public function suspendTenant(Tenant $tenant)
    {
        $tenant->update([
            'subscription_status' => 'suspended',
            'is_active' => false,
        ]);

        return back()->with('success', 'Tenant suspended successfully!');
    }

    /**
     * Reactivate tenant
     */
    public function reactivateTenant(Tenant $tenant)
    {
        $tenant->update([
            'subscription_status' => 'active',
            'is_active' => true,
        ]);

        return back()->with('success', 'Tenant reactivated successfully!');
    }

    /**
     * Delete tenant
     */
    public function destroyTenant(Tenant $tenant)
    {
        $tenant->delete();

        return redirect()
            ->route('platform.admin.tenants.index')
            ->with('success', 'Tenant deleted successfully!');
    }
}
