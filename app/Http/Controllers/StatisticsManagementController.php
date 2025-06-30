<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatisticsManagementController extends Controller
{
    public function index()
{
    $user = auth()->user();
    $outlet = \App\Models\Outlet::where('user_id', $user->id)->first();

    if (!$outlet) {
        return redirect()->route('dashboard')->with('error', 'Outlet tidak ditemukan untuk akun ini.');
    }

    $daysInMonth = now()->daysInMonth;
    $tanggal = range(1, $daysInMonth);

    // 📊 Ringkasan statistik
    $summaryStats = [
        ['label' => 'Total Surat', 'value' => \App\Models\Result::where('outlet_id', $outlet->id)->count()],
        ['label' => 'Perusahaan Aktif', 'value' => \App\Models\Patient::where('outlet_id', $outlet->id)->whereNotNull('company_id')->distinct('company_id')->count('company_id')],
        ['label' => 'Dokter Aktif', 'value' => \App\Models\Doctor::where('outlet_id', $outlet->id)->count()],
        ['label' => 'Pasien Aktif', 'value' => \App\Models\Patient::where('outlet_id', $outlet->id)->count()],
    ];

    // 📈 Grafik Surat Terbit per Hari
    $resultsPerDay = \App\Models\Result::selectRaw('DAY(created_at) as day, COUNT(*) as total')
        ->where('outlet_id', $outlet->id)
        ->whereMonth('created_at', now()->month)
        ->whereYear('created_at', now()->year)
        ->groupBy('day')
        ->pluck('total', 'day');

    $chartData = collect($tanggal)->map(fn($day) => $resultsPerDay[$day] ?? 0)->toArray();

    $outletChartData = [[
        'label' => $outlet->name,
        'data' => $chartData,
        'borderColor' => '#3B82F6',
    ]];

    // 🏥 Ranking Outlet (hanya outlet login)
    $outletRanking = [[
        'name' => $outlet->name,
        'bulan' => \App\Models\Result::where('outlet_id', $outlet->id)->whereMonth('created_at', now()->month)->count(),
        'total' => \App\Models\Result::where('outlet_id', $outlet->id)->count(),
    ]];

    // 👨‍⚕️ Ranking Dokter (Top 3)
    $doctorRanking = \App\Models\Doctor::with('user')
        ->where('outlet_id', $outlet->id)
        ->get()
        ->map(function ($doctor) {
            $total = $doctor->results()->count();
            $bulan = $doctor->results()->whereMonth('created_at', now()->month)->count();
            return [
                'name' => $doctor->user->name ?? '-',
                'bulan' => $bulan,
                'total' => $total,
            ];
        })
        ->sortByDesc('total')
        ->take(3)
        ->values()
        ->toArray();

    // 🏢 Ranking Perusahaan (Top 3 berdasarkan pasien)
    $companyRanking = \App\Models\Patient::where('outlet_id', $outlet->id)
        ->whereNotNull('company_id')
        ->with('company')
        ->get()
        ->groupBy('company_id')
        ->map(function ($group) {
            $total = $group->flatMap->results->count();
            $bulan = $group->flatMap->results->filter(fn($r) => $r->created_at->month == now()->month)->count();
            return [
                'name' => $group->first()->company->name ?? '-',
                'bulan' => $bulan,
                'total' => $total,
            ];
        })
        ->sortByDesc('total')
        ->take(3)
        ->values()
        ->toArray();

    // 🗺️ Maps Klinik
    $suratToday = \App\Models\Result::where('outlet_id', $outlet->id)
        ->whereDate('created_at', now())
        ->count();

    $mapKliniks = [[
        'name' => $outlet->name,
        'lat' => $outlet->latitude,
        'lon' => $outlet->longitude,
        'surat_count' => $suratToday,
        'has_new_surat' => $suratToday > 0
    ]];

    return view('outlets.statistics.index', [
        'summaryStats' => $summaryStats,
        'tanggal' => $tanggal,
        'outletChartData' => $outletChartData,
        'outletRanking' => $outletRanking,
        'doctorRanking' => $doctorRanking,
        'companyRanking' => $companyRanking,
        'mapKliniks' => $mapKliniks,
    ]);
}


    public function previewData(Request $request)
    {
        try {
            $data = $this->resolveData($request);

            return response()->json($data, 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Gagal mengambil data laporan.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
