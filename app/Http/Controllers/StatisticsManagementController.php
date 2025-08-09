<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Outlet;
use App\Models\Doctor;
use App\Models\Patient;
use App\Models\Result;
use Illuminate\Support\Facades\DB;

class StatisticsManagementController extends Controller
{
    /**
     * Menampilkan halaman statistik berdasarkan role user.
     * - Super Admin & Admin: Melihat data agregat dari semua outlet.
     * - Outlet: Hanya melihat data dari outlet miliknya.
     */
    public function index()
    {
        $user = auth()->user();

        // Pengecekan role untuk menentukan scope data
        if ($user->hasRoleType('superadmin') || $user->hasRoleType('admin')) {
            // --- LOGIC UNTUK SUPERADMIN & ADMIN ---
            return $this->showAllOutletsStatistics();
        } elseif ($user->hasRoleType('outlet')) {
            // --- LOGIC UNTUK OUTLET ---
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                return redirect()->route('dashboard')->with('error', 'Data outlet tidak ditemukan untuk akun Anda.');
            }
            return $this->showSingleOutletStatistics($outlet);
        } else {
            // Jika role tidak dikenali, kembalikan ke dashboard
            return redirect()->route('dashboard')->with('error', 'Anda tidak memiliki hak akses untuk halaman ini.');
        }
    }

    /**
     * Menyiapkan data statistik untuk SATU outlet spesifik.
     * @param Outlet $outlet
     * @return \Illuminate\View\View
     */
    private function showSingleOutletStatistics(Outlet $outlet)
    {
        $daysInMonth = now()->daysInMonth;
        $tanggal = range(1, $daysInMonth);

        // 📊 Ringkasan statistik untuk satu outlet
        $summaryStats = [
            ['label' => 'Total Surat', 'value' => Result::where('outlet_id', $outlet->id)->count()],
            ['label' => 'Perusahaan Aktif', 'value' => Patient::where('outlet_id', $outlet->id)->whereNotNull('company_id')->distinct('company_id')->count('company_id')],
            ['label' => 'Dokter Aktif', 'value' => Doctor::where('outlet_id', $outlet->id)->count()],
            ['label' => 'Pasien Terdaftar', 'value' => Patient::where('outlet_id', $outlet->id)->count()],
        ];

        // 📈 Grafik Surat Terbit per Hari untuk satu outlet
        $resultsPerDay = Result::selectRaw('DAY(created_at) as day, COUNT(*) as total')
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

        // 🏥 Ranking Outlet (hanya menampilkan outlet yang login)
        $outletRanking = [[
            'name' => $outlet->name,
            'bulan' => Result::where('outlet_id', $outlet->id)->whereMonth('created_at', now()->month)->count(),
            'total' => Result::where('outlet_id', $outlet->id)->count(),
        ]];

        // 👨‍⚕️ Ranking Dokter (Top 3 di outlet ini)
        $doctorRanking = $this->getDoctorRankingQuery()->where('outlet_id', $outlet->id)->take(3)->get();

        // 🏢 Ranking Perusahaan (Top 3 di outlet ini)
        $companyRanking = $this->getCompanyRankingQuery()->where('patients.outlet_id', $outlet->id)->take(3)->get();
        
        // 🗺️ Data Peta untuk outlet ini
        $suratToday = Result::where('outlet_id', $outlet->id)->whereDate('created_at', now())->count();
        $mapKliniks = [[
            'name' => $outlet->name,
            'lat' => $outlet->latitude,
            'lon' => $outlet->longitude,
            'surat_count' => $suratToday,
            'has_new_surat' => $suratToday > 0
        ]];

        return view('superadmin.statistics.index', compact('summaryStats', 'tanggal', 'outletChartData', 'outletRanking', 'doctorRanking', 'companyRanking', 'mapKliniks'));
    }

    /**
     * Menyiapkan data statistik agregat dari SEMUA outlet.
     * @return \Illuminate\View\View
     */
    private function showAllOutletsStatistics()
    {
        $daysInMonth = now()->daysInMonth;
        $tanggal = range(1, $daysInMonth);

        // 📊 Ringkasan statistik gabungan
        $summaryStats = [
            ['label' => 'Total Surat Terbit', 'value' => Result::count()],
            ['label' => 'Total Perusahaan', 'value' => \App\Models\Company::count()],
            ['label' => 'Total Dokter', 'value' => Doctor::count()],
            ['label' => 'Total Pasien', 'value' => Patient::count()],
        ];

        // 📈 Grafik Surat Terbit per Hari untuk semua outlet
        $resultsPerDay = Result::selectRaw('outlet_id, DAY(created_at) as day, COUNT(*) as total')
            ->with('outlet:id,name')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->groupBy('outlet_id', 'day')
            ->get();

        $outlets = Outlet::all();
        $colors = ['#3B82F6', '#EF4444', '#10B981', '#F59E0B', '#8B5CF6', '#EC4899'];
        $outletChartData = $outlets->map(function ($outlet, $index) use ($resultsPerDay, $tanggal, $colors) {
            $dataForOutlet = $resultsPerDay->where('outlet_id', $outlet->id)->pluck('total', 'day');
            return [
                'label' => $outlet->name,
                'data' => collect($tanggal)->map(fn($day) => $dataForOutlet[$day] ?? 0)->toArray(),
                'borderColor' => $colors[$index % count($colors)],
            ];
        })->toArray();

        // 🏥 Ranking Outlet (Top 5)
        $outletRanking = Outlet::withCount(['results as bulan' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->withCount('results as total')
            ->orderByDesc('total')
            ->take(5)
            ->get();
        
        // 👨‍⚕️ Ranking Dokter (Top 5 dari semua outlet)
        $doctorRanking = $this->getDoctorRankingQuery()->take(5)->get();

        // 🏢 Ranking Perusahaan (Top 5 dari semua outlet)
        $companyRanking = $this->getCompanyRankingQuery()->take(5)->get();

        // 🗺️ Data Peta untuk semua klinik
        $resultsToday = Result::selectRaw('outlet_id, COUNT(*) as count')
            ->whereDate('created_at', now())
            ->groupBy('outlet_id')
            ->pluck('count', 'outlet_id');
            
        $mapKliniks = $outlets->map(function ($outlet) use ($resultsToday) {
            $suratToday = $resultsToday->get($outlet->id, 0);
            return [
                'name' => $outlet->name,
                'lat' => $outlet->latitude,
                'lon' => $outlet->longitude,
                'surat_count' => $suratToday,
                'has_new_surat' => $suratToday > 0,
            ];
        })->toArray();

        return view('superadmin.statistics.index', compact('summaryStats', 'tanggal', 'outletChartData', 'outletRanking', 'doctorRanking', 'companyRanking', 'mapKliniks'));
    }

    /**
     * Query dasar untuk ranking dokter.
     */
    private function getDoctorRankingQuery()
    {
        return Doctor::select('doctors.*', 'users.name as name')
            ->join('users', 'users.id', '=', 'doctors.user_id')
            ->withCount(['results as bulan' => function ($query) {
                $query->whereMonth('created_at', now()->month);
            }])
            ->withCount('results as total')
            ->orderByDesc('total');
    }

    /**
     * Query dasar untuk ranking perusahaan.
     */
    private function getCompanyRankingQuery()
    {
        return \App\Models\Company::select('companies.name', 
                DB::raw('COUNT(DISTINCT results.id) as total'), 
                DB::raw('SUM(CASE WHEN MONTH(results.created_at) = '.now()->month.' THEN 1 ELSE 0 END) as bulan')
            )
            ->join('patients', 'companies.id', '=', 'patients.company_id')
            ->join('results', 'patients.id', '=', 'results.patient_id')
            ->groupBy('companies.id', 'companies.name')
            ->orderByDesc('total');
    }
}
