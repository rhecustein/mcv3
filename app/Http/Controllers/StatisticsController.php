<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Company;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Outlet;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StatisticsManagementController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $outlet = Outlet::where('user_id', $user->id)->first();

        if (!$outlet) {
            return redirect()->route('dashboard')->with('error', 'Outlet tidak ditemukan untuk akun ini.');
        }

        $currentMonth = now()->month;
        $currentYear = now()->year;
        $daysInMonth = now()->daysInMonth;
        $tanggal = range(1, $daysInMonth);

        // 📊 Ringkasan statistik dengan data real
        $totalLetters = Result::where('outlet_id', $outlet->id)->count();
        $activeCompanies = Patient::where('outlet_id', $outlet->id)
            ->whereNotNull('company_id')
            ->distinct('company_id')
            ->count('company_id');
        $activeDoctors = Doctor::where('outlet_id', $outlet->id)->count();
        $activePatients = Patient::where('outlet_id', $outlet->id)->count();

        $summaryStats = [
            ['label' => 'Total Surat', 'value' => $totalLetters],
            ['label' => 'Perusahaan Aktif', 'value' => $activeCompanies],
            ['label' => 'Dokter Aktif', 'value' => $activeDoctors],
            ['label' => 'Pasien Aktif', 'value' => $activePatients],
        ];

        // 📈 Grafik Surat Terbit per Hari (data real)
        $resultsPerDay = Result::selectRaw('DAY(created_at) as day, COUNT(*) as total')
            ->where('outlet_id', $outlet->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->groupBy('day')
            ->pluck('total', 'day');

        $chartData = collect($tanggal)->map(function($day) use ($resultsPerDay) {
            return $resultsPerDay[$day] ?? 0;
        })->toArray();

        $outletChartData = [[
            'label' => $outlet->name,
            'data' => $chartData,
            'borderColor' => '#3B82F6',
        ]];

        // 🏥 Ranking Outlet (data outlet saat ini)
        $monthlyCount = Result::where('outlet_id', $outlet->id)
            ->whereMonth('created_at', $currentMonth)
            ->whereYear('created_at', $currentYear)
            ->count();
        
        $outletRanking = [[
            'name' => $outlet->name,
            'bulan' => $monthlyCount,
            'total' => $totalLetters,
        ]];

        // 👨‍⚕️ Ranking Dokter (Top 5 berdasarkan data real)
        $doctorRanking = Doctor::with('user')
            ->where('outlet_id', $outlet->id)
            ->get()
            ->map(function ($doctor) use ($currentMonth, $currentYear) {
                $totalResults = Result::where('doctor_id', $doctor->id)->count();
                $monthlyResults = Result::where('doctor_id', $doctor->id)
                    ->whereMonth('created_at', $currentMonth)
                    ->whereYear('created_at', $currentYear)
                    ->count();
                
                return [
                    'name' => $doctor->user->name ?? 'Dokter Tidak Diketahui',
                    'bulan' => $monthlyResults,
                    'total' => $totalResults,
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values()
            ->toArray();

        // 🏢 Ranking Perusahaan (Top 5 berdasarkan aktivitas pasien)
        $companyRanking = Patient::where('outlet_id', $outlet->id)
            ->whereNotNull('company_id')
            ->with(['company', 'results'])
            ->get()
            ->groupBy('company_id')
            ->map(function ($patients) use ($currentMonth, $currentYear) {
                $company = $patients->first()->company;
                if (!$company) return null;
                
                // Hitung total surat dari semua pasien perusahaan ini
                $totalResults = $patients->flatMap(function($patient) {
                    return $patient->results;
                })->count();
                
                // Hitung surat bulan ini
                $monthlyResults = $patients->flatMap(function($patient) use ($currentMonth, $currentYear) {
                    return $patient->results->filter(function($result) use ($currentMonth, $currentYear) {
                        return $result->created_at->month == $currentMonth && 
                               $result->created_at->year == $currentYear;
                    });
                })->count();
                
                return [
                    'name' => $company->name,
                    'bulan' => $monthlyResults,
                    'total' => $totalResults,
                ];
            })
            ->filter() // Remove null values
            ->sortByDesc('total')
            ->take(5)
            ->values()
            ->toArray();

        // 🗺️ Data untuk peta (koordinat outlet)
        $suratToday = Result::where('outlet_id', $outlet->id)
            ->whereDate('created_at', now())
            ->count();

        $mapKliniks = [[
            'name' => $outlet->name,
            'lat' => $outlet->latitude ?? 1.1, // Default koordinat jika tidak ada
            'lon' => $outlet->longitude ?? 104.05,
            'surat_count' => $suratToday,
            'has_new_surat' => $suratToday > 0
        ]];

        return view('outlet.statistics.index', [
            'summaryStats' => $summaryStats,
            'tanggal' => $tanggal,
            'outletChartData' => $outletChartData,
            'outletRanking' => $outletRanking,
            'doctorRanking' => $doctorRanking,
            'companyRanking' => $companyRanking,
            'mapKliniks' => $mapKliniks,
        ]);
    }

    /**
     * Get additional statistics for API calls
     */
    public function getDetailedStats(Request $request)
    {
        $user = auth()->user();
        $outlet = Outlet::where('user_id', $user->id)->first();

        if (!$outlet) {
            return response()->json(['error' => 'Outlet tidak ditemukan'], 404);
        }

        $period = $request->get('period', 'this_month'); // today, this_week, this_month, all_time
        $now = now();

        // Determine date range based on period
        switch ($period) {
            case 'today':
                $startDate = $now->copy()->startOfDay();
                $endDate = $now->copy()->endOfDay();
                break;
            case 'this_week':
                $startDate = $now->copy()->startOfWeek();
                $endDate = $now->copy()->endOfWeek();
                break;
            case 'this_month':
                $startDate = $now->copy()->startOfMonth();
                $endDate = $now->copy()->endOfMonth();
                break;
            default: // all_time
                $startDate = null;
                $endDate = null;
        }

        // Build query
        $query = Result::where('outlet_id', $outlet->id);
        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // Get statistics
        $totalResults = $query->count();
        $skbCount = $query->clone()->where('type', 'skb')->count();
        $mcCount = $query->clone()->where('type', 'mc')->count();

        // Doctor performance
        $doctorStats = Doctor::where('outlet_id', $outlet->id)
            ->with('user')
            ->get()
            ->map(function ($doctor) use ($startDate, $endDate) {
                $doctorQuery = Result::where('doctor_id', $doctor->id);
                if ($startDate && $endDate) {
                    $doctorQuery->whereBetween('created_at', [$startDate, $endDate]);
                }
                
                return [
                    'id' => $doctor->id,
                    'name' => $doctor->user->name ?? 'Unknown',
                    'total_results' => $doctorQuery->count(),
                    'skb_count' => $doctorQuery->clone()->where('type', 'skb')->count(),
                    'mc_count' => $doctorQuery->clone()->where('type', 'mc')->count(),
                ];
            })
            ->sortByDesc('total_results');

        // Company statistics
        $companyStats = Patient::where('outlet_id', $outlet->id)
            ->whereNotNull('company_id')
            ->with(['company'])
            ->get()
            ->groupBy('company_id')
            ->map(function ($patients) use ($startDate, $endDate) {
                $company = $patients->first()->company;
                if (!$company) return null;
                
                $patientIds = $patients->pluck('id');
                $companyQuery = Result::whereIn('patient_id', $patientIds);
                
                if ($startDate && $endDate) {
                    $companyQuery->whereBetween('created_at', [$startDate, $endDate]);
                }
                
                return [
                    'id' => $company->id,
                    'name' => $company->name,
                    'total_results' => $companyQuery->count(),
                    'total_patients' => $patients->count(),
                    'skb_count' => $companyQuery->clone()->where('type', 'skb')->count(),
                    'mc_count' => $companyQuery->clone()->where('type', 'mc')->count(),
                ];
            })
            ->filter()
            ->sortByDesc('total_results');

        return response()->json([
            'period' => $period,
            'date_range' => [
                'start' => $startDate ? $startDate->format('Y-m-d') : null,
                'end' => $endDate ? $endDate->format('Y-m-d') : null,
            ],
            'summary' => [
                'total_results' => $totalResults,
                'skb_count' => $skbCount,
                'mc_count' => $mcCount,
                'success_rate' => $totalResults > 0 ? round(($totalResults / $totalResults) * 100, 2) : 0,
            ],
            'doctor_stats' => $doctorStats->values(),
            'company_stats' => $companyStats->values(),
        ]);
    }

    /**
     * Export statistics data
     */
    public function exportStats(Request $request)
    {
        $user = auth()->user();
        $outlet = Outlet::where('user_id', $user->id)->first();

        if (!$outlet) {
            return redirect()->back()->with('error', 'Outlet tidak ditemukan');
        }

        $period = $request->get('period', 'this_month');
        $format = $request->get('format', 'csv'); // csv, excel, pdf

        // Get data using the same logic as getDetailedStats
        $statsData = $this->getDetailedStats($request)->getData();

        // Here you would implement the export logic based on format
        // For now, returning JSON response
        return response()->json([
            'message' => 'Export functionality will be implemented',
            'data' => $statsData,
            'format' => $format
        ]);
    }
}