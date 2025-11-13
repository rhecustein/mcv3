<?php

namespace App\Http\Controllers\Platform;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\TenantInvoice;
use App\Models\SubscriptionPlan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    /**
     * Display billing dashboard
     */
    public function index()
    {
        $stats = [
            'total_revenue_this_month' => TenantInvoice::whereMonth('invoice_date', now()->month)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'total_revenue_last_month' => TenantInvoice::whereMonth('invoice_date', now()->subMonth()->month)
                ->where('status', 'paid')
                ->sum('total_amount'),
            'pending_amount' => TenantInvoice::whereIn('status', ['sent', 'draft'])
                ->sum('total_amount'),
            'overdue_amount' => TenantInvoice::where('status', 'overdue')
                ->sum('total_amount'),
            'total_invoices' => TenantInvoice::count(),
            'paid_invoices' => TenantInvoice::where('status', 'paid')->count(),
            'overdue_invoices' => TenantInvoice::where('status', 'overdue')->count(),
        ];

        // Recent invoices
        $recentInvoices = TenantInvoice::with('tenant')
            ->latest()
            ->limit(20)
            ->get();

        // Revenue by plan
        $revenueByPlan = Tenant::select('subscription_plan', DB::raw('COUNT(*) as tenant_count'))
            ->where('is_active', true)
            ->groupBy('subscription_plan')
            ->get();

        return view('platform.billing.index', compact('stats', 'recentInvoices', 'revenueByPlan'));
    }

    /**
     * Display all invoices with filters
     */
    public function invoices(Request $request)
    {
        $query = TenantInvoice::with('tenant');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('tenant_id')) {
            $query->where('tenant_id', $request->tenant_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('invoice_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('invoice_date', '<=', $request->date_to);
        }

        $invoices = $query->latest('invoice_date')->paginate(20);
        $tenants = Tenant::where('is_active', true)->get();

        return view('platform.billing.invoices', compact('invoices', 'tenants'));
    }

    /**
     * Show invoice details
     */
    public function showInvoice(TenantInvoice $invoice)
    {
        $invoice->load('tenant');
        return view('platform.billing.show', compact('invoice'));
    }

    /**
     * Generate invoices for all active tenants
     */
    public function generateInvoices()
    {
        $generated = 0;
        $errors = [];

        $tenants = Tenant::where('is_active', true)
            ->where('subscription_status', 'active')
            ->get();

        foreach ($tenants as $tenant) {
            try {
                // Check if invoice already exists for this month
                $exists = TenantInvoice::where('tenant_id', $tenant->id)
                    ->whereMonth('invoice_date', now()->month)
                    ->whereYear('invoice_date', now()->year)
                    ->exists();

                if ($exists) {
                    continue;
                }

                // Get plan pricing
                $plan = SubscriptionPlan::where('slug', $tenant->subscription_plan)->first();

                if (!$plan) {
                    $errors[] = "Plan not found for tenant: {$tenant->name}";
                    continue;
                }

                // Create invoice
                TenantInvoice::create([
                    'tenant_id' => $tenant->id,
                    'invoice_number' => TenantInvoice::generateInvoiceNumber(),
                    'invoice_date' => now(),
                    'due_date' => now()->addDays(14),
                    'total_amount' => $plan->price_monthly,
                    'amount_paid' => 0,
                    'status' => 'draft',
                    'description' => "Subscription - {$plan->name} - " . now()->format('F Y'),
                    'items' => [
                        [
                            'description' => "{$plan->name} Plan Subscription",
                            'quantity' => 1,
                            'unit_price' => $plan->price_monthly,
                            'total' => $plan->price_monthly,
                        ]
                    ],
                ]);

                $generated++;
            } catch (\Exception $e) {
                $errors[] = "Error for tenant {$tenant->name}: " . $e->getMessage();
            }
        }

        if (count($errors) > 0) {
            return back()->with('warning', "Generated {$generated} invoices with " . count($errors) . " errors.");
        }

        return back()->with('success', "Successfully generated {$generated} invoices!");
    }

    /**
     * Mark invoice as sent
     */
    public function sendInvoice(TenantInvoice $invoice)
    {
        $invoice->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);

        // Here you would typically send email notification
        // Mail::to($invoice->tenant->email)->send(new InvoiceMail($invoice));

        return back()->with('success', 'Invoice marked as sent!');
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid(TenantInvoice $invoice, Request $request)
    {
        $validated = $request->validate([
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'payment_reference' => 'nullable|string',
        ]);

        $invoice->update([
            'status' => 'paid',
            'amount_paid' => $invoice->total_amount,
            'paid_at' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'payment_reference' => $validated['payment_reference'] ?? null,
        ]);

        return back()->with('success', 'Invoice marked as paid!');
    }

    /**
     * Mark overdue invoices
     */
    public function markOverdueInvoices()
    {
        $count = TenantInvoice::where('status', 'sent')
            ->where('due_date', '<', now())
            ->update(['status' => 'overdue']);

        return back()->with('success', "Marked {$count} invoices as overdue!");
    }

    /**
     * Cancel invoice
     */
    public function cancelInvoice(TenantInvoice $invoice)
    {
        $invoice->update(['status' => 'cancelled']);
        return back()->with('success', 'Invoice cancelled!');
    }
}
