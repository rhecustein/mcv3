<?php

namespace App\Http\Controllers;

use App\Models\Result;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\SessionLogin;
use Illuminate\Support\Facades\DB;

class StatisticsController extends Controller
{

    public function index()
    {
        // 📊 Ringkasan Umum
        $totalResults   = Result::count();
        $totalSKB       = Result::where('type', 'skb')->count();
        $totalMC        = Result::where('type', 'mc')->count();
        $totalCompanies = Company::where('is_active', true)->count();

        // 🏅 Ranking Outlet (Top 10)
        $outletRanks = Result::select('outlets.name', DB::raw('COUNT(results.id) as total'))
            ->join('outlets', 'results.outlet_id', '=', 'outlets.id')
            ->groupBy('outlets.name')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // 📍 Koordinat Surat (Riau)
        $resultCoordinates = Result::select(
                'created_latitude',
                'created_longitude',
                'created_city',
                'outlets.name as outlet_name'
            )
            ->join('outlets', 'results.outlet_id', '=', 'outlets.id')
            ->join('admins', 'outlets.admin_id', '=', 'admins.id')
            ->whereNotNull('created_latitude')
            ->whereNotNull('created_longitude')
            ->whereRaw("LOWER(admins.province) = 'riau'")
            ->get();

        // 🔐 Seluruh Lokasi Login (muat relasi user)
        $sessionLogins = SessionLogin::with('user:id,name') // Eager load user name
            ->select('user_id', 'latitude', 'longitude', 'city', 'ip_address', 'logged_in_at')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('success', true)
            ->orderByDesc('logged_in_at')
            ->get();

        // 🏘️ Demografi Kota (Riau)
        $citySummary = Result::select('created_city', DB::raw('COUNT(*) as total'))
            ->join('outlets', 'results.outlet_id', '=', 'outlets.id')
            ->join('admins', 'outlets.admin_id', '=', 'admins.id')
            ->whereRaw("LOWER(admins.province) = 'riau'")
            ->whereNotNull('created_city')
            ->groupBy('created_city')
            ->orderByDesc('total')
            ->get();

        $cityLabels = $citySummary->pluck('created_city');
        $cityData   = $citySummary->pluck('total');
        $maxCity    = $citySummary->first();
        $minCity    = $citySummary->last();

        return view('superadmin.statistics', compact(
            'totalResults', 'totalSKB', 'totalMC', 'totalCompanies',
            'outletRanks', 'resultCoordinates', 'sessionLogins',
            'cityLabels', 'cityData', 'maxCity', 'minCity'
        ));
    }

    public function leaderboard(Request $request)
    {
        // Realtime Rank Tanpa Limit
        $leaderboard = Result::select('outlets.name', DB::raw('COUNT(results.id) as total'))
            ->join('outlets', 'results.outlet_id', '=', 'outlets.id')
            ->groupBy('outlets.name')
            ->orderByDesc('total')
            ->get();

        return view('superadmin.leaderboard', compact('leaderboard'));
    }

    public function getLeaderboard()
    {
        $now = now();
        $today = $now->toDateString();
        $startOfWeek = $now->startOfWeek()->toDateString();
        $startOfMonth = $now->startOfMonth()->toDateString();

        // Count global summary
        $countAll = Result::count();
        $countToday = Result::whereDate('created_at', $today)->count();
        $countMonth = Result::whereDate('created_at', '>=', $startOfMonth)->count();

        // Per outlet leaderboard
        $ranks = Result::select('outlets.id', 'outlets.name')
            ->join('outlets', 'results.outlet_id', '=', 'outlets.id')
            ->selectRaw('COUNT(results.id) as total_all')
            ->selectRaw("SUM(CASE WHEN results.type = 'mc' THEN 1 ELSE 0 END) as total_mc")
            ->selectRaw("SUM(CASE WHEN results.type = 'skb' THEN 1 ELSE 0 END) as total_skb")
            ->selectRaw('COUNT(DISTINCT results.patient_id) as total_patients')
            ->selectRaw('COUNT(DISTINCT results.doctor_id) as total_doctors')
            ->selectRaw('COUNT(DISTINCT results.company_id) as total_companies')
            ->selectRaw("SUM(CASE WHEN DATE(results.created_at) = ? THEN 1 ELSE 0 END) as total_today", [$today])
            ->selectRaw("SUM(CASE WHEN DATE(results.created_at) >= ? THEN 1 ELSE 0 END) as total_week", [$startOfWeek])
            ->selectRaw("SUM(CASE WHEN DATE(results.created_at) >= ? THEN 1 ELSE 0 END) as total_month", [$startOfMonth])
            ->groupBy('outlets.id', 'outlets.name')
            ->orderByDesc('total_all')
            ->limit(25)
            ->get();

        return response()->json([
            'count_all' => $countAll,
            'count_today' => $countToday,
            'count_month' => $countMonth,
            'ranks' => $ranks
        ]);
    }   
}
