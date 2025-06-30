<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Doctor;
use App\Models\Result;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AdminManagementController extends Controller
{
    public function index(Request $request)
    {
        // Ambil daftar admin dengan relasi user, filter, dan paginasi
        $admins = Admin::with('user')
            ->when($request->search, fn($q) =>
                $q->whereHas('user', fn($uq) =>
                    $uq->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%")
                ))
            ->when($request->province, fn($q) =>
                $q->where('province', $request->province))
            ->latest()
            ->paginate(15);

        // Hitung data terkait per admin
        foreach ($admins as $admin) {
            $admin->user_count = User::where('role_type', 'patient')
                ->whereHas('patient', fn($q) =>
                    $q->where('admin_id', $admin->id))
                ->count();

            $admin->outlet_count = Outlet::where('admin_id', $admin->id)->count();
            $admin->doctor_count = Doctor::where('admin_id', $admin->id)->count();

            // Fix: hitung jumlah surat berdasarkan outlet yang dimiliki admin
            $admin->letter_count = Result::whereIn('outlet_id', function ($q) use ($admin) {
                $q->select('id')
                ->from('outlets')
                ->where('admin_id', $admin->id);
            })->count();
        }

        $provinces = Admin::select('province')
            ->whereNotNull('province')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        $totalAdmins = Admin::count();

        return view('superadmin.admins.index', compact(
            'admins', 'provinces', 'totalAdmins'
        ));
    }


    public function create()
    {
        return view('superadmin.admins.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users',
            'phone' => 'nullable|string|max:20',
            'password' => 'nullable|min:6',
            'region_name' => 'required|string|max:100',
            'province' => 'nullable|string|max:50',
            'position_title' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
        ]);

        // Simpan user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'password' => Hash::make($validated['password'] ?? 'admin123'),
            'role_type' => 'admin',
        ]);

        // Simpan admin
        Admin::create([
            'user_id' => $user->id,
            'region_name' => $validated['region_name'],
            'province' => $validated['province'],
            'position_title' => $validated['position_title'],
            'contact_number' => $validated['contact_number'],
        ]);

        return redirect()->route('admins.index')->with('success', 'Admin berhasil ditambahkan.');
    }

    public function edit(Admin $admin)
    {
        $admin->load('user');
        return view('superadmin.admins.edit', compact('admin'));
    }

    public function update(Request $request, Admin $admin)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => ['required','email', Rule::unique('users')->ignore($admin->user_id)],
            'phone' => 'nullable|string|max:20',
            'region_name' => 'required|string|max:100',
            'province' => 'nullable|string|max:50',
            'position_title' => 'nullable|string|max:100',
            'contact_number' => 'nullable|string|max:20',
        ]);

        $admin->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
        ]);

        $admin->update([
            'region_name' => $validated['region_name'],
            'province' => $validated['province'],
            'position_title' => $validated['position_title'],
            'contact_number' => $validated['contact_number'],
        ]);

        return redirect()->route('admins.index')->with('success', 'Data admin diperbarui.');
    }

    public function destroy(Admin $admin)
    {
        $admin->user()->delete(); // soft delete user
        $admin->delete(); // soft delete admin
        return redirect()->route('admins.index')->with('success', 'Admin berhasil dihapus.');
    }

    public function ban(User $user)
    {
        // Cegah memban superadmin demi keamanan sistem
        if ($user->role_type === 'superadmin') {
            abort(403, 'Tidak diperbolehkan memblokir akun superadmin.');
        }

        $user->is_active = false;
        $user->save();

        return redirect()->route('admins.index')->with('success', 'Akun admin berhasil dinonaktifkan.');
    }

    public function unban(User $user)
    {
        if ($user->role_type === 'superadmin') {
            abort(403, 'Tidak diperbolehkan mengubah status superadmin.');
        }

        $user->is_active = true;
        $user->save();

        return redirect()->route('admins.index')->with('success', 'Akun admin berhasil diaktifkan kembali.');
    }

    //dashboard
public function dashboard()
{
    // Statistik total entity
    $totalResults = Result::count();
    $totalSKB = Result::where('type', 'skb')->count();
    $totalMC = Result::where('type', 'mc')->count();
    $totalAdmins = Admin::count();
    $totalOutlets = Outlet::count();
    $totalDoctors = Doctor::count();
    $totalPatients = User::where('role_type', 'patient')->count();
    $totalCompanies = Company::count();

    // Statistik outlet: jumlah surat per outlet
    $outletStats = Outlet::withCount('results')
        ->get()
        ->map(fn($outlet) => (object)[
            'name' => $outlet->name,
            'count' => $outlet->results_count,
        ]);

    // Trend bulanan: 6 bulan terakhir
    $months = collect(range(0, 5))->map(
        fn($i) => now()->subMonths($i)->format('Y-m')
    )->reverse();

    $trendData = $months->map(fn($month) =>
        Result::whereYear('created_at', substr($month, 0, 4))
            ->whereMonth('created_at', substr($month, 5, 2))
            ->count()
    );

    $trendLabels = $months->map(fn($month) =>
        \Carbon\Carbon::createFromFormat('Y-m', $month)->translatedFormat('F Y')
    );

    // Distribusi Surat per Provinsi (admin->province → outlet → surat)
    $provinceLabels = Admin::whereNotNull('province')
        ->distinct()
        ->pluck('province');

    $provinceData = $provinceLabels->map(function ($province) {
        return Result::whereIn('outlet_id', function ($q) use ($province) {
            $q->select('id')->from('outlets')->whereIn('admin_id', function ($sub) use ($province) {
                $sub->select('id')->from('admins')->where('province', $province);
            });
        })->count();
    });

    // Top 5 Admin Aktif (berdasarkan jumlah outlet)
    $topAdmins = Admin::with(['user'])
        ->withCount(['outlets', 'doctors'])
        ->withCount(['patients as patients_count' => function ($q) {
            $q->whereHas('user', fn($q) => $q->where('role_type', 'patient'));
        }])
        ->orderByDesc('outlets_count')
        ->limit(5)
        ->get();

    // Aktivitas Terbaru: 10 surat terakhir
    $recentResults = Result::with(['patient.user', 'outlet'])
        ->latest()
        ->limit(10)
        ->get();

    return view('superadmin.dashboard', compact(
        'totalResults', 'totalSKB', 'totalMC',
        'totalAdmins', 'totalOutlets', 'totalDoctors', 'totalPatients', 'totalCompanies',
        'outletStats', 'trendLabels', 'trendData',
        'provinceLabels', 'provinceData',
        'topAdmins', 'recentResults'
    ));
}


}
