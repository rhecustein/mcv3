<?php

namespace App\Http\Controllers\Corporate;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\CorporateEmployee;
use App\Models\CorporateHealthReport;
use App\Models\McuBooking;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display corporate dashboard
     */
    public function index(Request $request)
    {
        $tenant = app('tenant');
        $user = auth()->user();

        // Get user's company (assuming companies role)
        $company = Company::where('tenant_id', $tenant->id)
            ->whereHas('companyAdmins', function($q) use ($user) {
                $q->where('id', $user->id);
            })
            ->first();

        if (!$company) {
            abort(403, 'No company access');
        }

        // Statistics
        $stats = [
            'total_employees' => CorporateEmployee::where('company_id', $company->id)->count(),
            'active_employees' => CorporateEmployee::where('company_id', $company->id)->active()->count(),
            'mcu_overdue' => CorporateEmployee::where('company_id', $company->id)->mcuOverdue()->count(),
            'mcu_this_month' => McuBooking::where('company_id', $company->id)
                ->whereMonth('booking_date', now()->month)
                ->count(),
        ];

        // Recent employees
        $recentEmployees = CorporateEmployee::where('company_id', $company->id)
            ->latest()
            ->limit(10)
            ->get();

        // MCU Status Distribution
        $healthDistribution = CorporateEmployee::where('company_id', $company->id)
            ->selectRaw('health_status, COUNT(*) as count')
            ->groupBy('health_status')
            ->get()
            ->pluck('count', 'health_status');

        // Recent reports
        $recentReports = CorporateHealthReport::where('company_id', $company->id)
            ->published()
            ->latest()
            ->limit(5)
            ->get();

        return view('corporate.dashboard', compact(
            'company',
            'stats',
            'recentEmployees',
            'healthDistribution',
            'recentReports'
        ));
    }

    /**
     * Employee list
     */
    public function employees(Request $request)
    {
        $tenant = app('tenant');
        $user = auth()->user();

        $company = Company::where('tenant_id', $tenant->id)
            ->whereHas('companyAdmins', function($q) use ($user) {
                $q->where('id', $user->id);
            })
            ->firstOrFail();

        $query = CorporateEmployee::where('company_id', $company->id);

        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('employee_id', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        }

        if ($request->filled('department')) {
            $query->where('department', $request->department);
        }

        if ($request->filled('health_status')) {
            $query->where('health_status', $request->health_status);
        }

        $employees = $query->orderBy('created_at', 'desc')->paginate(20);

        $departments = CorporateEmployee::where('company_id', $company->id)
            ->select('department')
            ->distinct()
            ->pluck('department');

        return view('corporate.employees.index', compact('company', 'employees', 'departments'));
    }

    /**
     * Health reports list
     */
    public function reports(Request $request)
    {
        $tenant = app('tenant');
        $user = auth()->user();

        $company = Company::where('tenant_id', $tenant->id)
            ->whereHas('companyAdmins', function($q) use ($user) {
                $q->where('id', $user->id);
            })
            ->firstOrFail();

        $reports = CorporateHealthReport::where('company_id', $company->id)
            ->published()
            ->latest()
            ->paginate(10);

        return view('corporate.reports.index', compact('company', 'reports'));
    }
}
