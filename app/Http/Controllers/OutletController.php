<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Outlet;
use App\Models\Result;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log; // Tetap gunakan Log jika diperlukan

class OutletController extends Controller
{
    /**
     * Menampilkan dashboard untuk role 'outlet'.
     */
    public function dashboard()
    {
        $user = auth()->user();
        // firstOrFail akan otomatis menampilkan 404 jika tidak ditemukan
        $outlet = Outlet::where('email', $user->email)->firstOrFail();

        $totalDoctors = $outlet->doctors()->count();
        $totalLetters = $outlet->results()->count();

        $latestLetters = $outlet->results()
            ->with(['patient.user', 'doctor.user'])
            ->latest()
            ->take(5)
            ->get();

        $monthlyLetters = $outlet->results()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subYear()) // Optimasi: Hanya 1 tahun terakhir
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get();

        $labels = $monthlyLetters->pluck('month')->map(fn($month) => Carbon::parse($month . '-01')->translatedFormat('F Y'));
        $data = $monthlyLetters->pluck('total');

        return view('outlets.dashboard', compact('outlet', 'totalDoctors', 'totalLetters', 'latestLetters', 'labels', 'data'));
    }

    /**
     * Menampilkan daftar semua outlet untuk Superadmin.
     */
    public function index(Request $request)
    {
        // Query dasar yang bisa digunakan kembali
        $baseQuery = Outlet::query();

        // Data untuk kartu statistik
        $totalOutlets = (clone $baseQuery)->count();
        $bannedOutlets = (clone $baseQuery)->where('is_active', false)->count();
        $totalLetters = Result::count();
        
        // Query utama untuk tabel/grid
        $outletsQuery = Outlet::with(['admin.user'])
            ->withCount(['doctors', 'results as letter_count'])
            ->when($request->search, function ($q, $search) {
                $q->where(function ($sub) use ($search) {
                    $sub->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('city', 'like', "%{$search}%")
                        ->orWhereHas('admin.user', fn($u) => $u->where('name', 'like', "%{$search}%"));
                });
            })
            ->when($request->filled('is_active'), fn($q) => $q->where('is_active', $request->is_active));
        
        $provinces = (clone $baseQuery)->select('province')
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        $outlets = $outletsQuery->latest()->paginate(10);

        return view('superadmin.outlets.index', compact(
            'outlets',
            'totalOutlets',
            'bannedOutlets',
            'totalLetters',
            'provinces'
        ));
    }

    /**
     * Menampilkan form tambah outlet.
     */
    public function create()
    {
        // Optimasi: Hanya ambil admin yang aktif dan urutkan
        $admins = Admin::with('user')->whereHas('user', fn($q) => $q->where('is_active', true))
            ->get()->sortBy('user.name');
        return view('superadmin.outlets.create', compact('admins'));
    }

    /**
     * Menyimpan outlet baru.
     * PENINGKATAN: Menggunakan DB Transaction untuk keamanan data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'nullable|email|max:100|unique:users,email|unique:outlets,email',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
            'province'  => 'nullable|string|max:50',
            'city'      => 'nullable|string|max:50',
            'admin_id'  => 'nullable|exists:admins,id',
            'is_active' => 'boolean' // validasi boolean lebih aman untuk checkbox
        ]);

        try {
            DB::beginTransaction();

            // Buat user baru untuk outlet jika email diisi
            if (!empty($validated['email'])) {
                User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('default123'), // Password default
                    'role_type' => 'outlet',
                    'is_active' => $validated['is_active'] ?? true,
                ]);
            }

            Outlet::create($validated);
            
            DB::commit();
            return redirect()->route('outlets.index')->with('success', 'Outlet baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat outlet: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan data. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menampilkan form edit outlet.
     */
    public function edit(Outlet $outlet)
    {
        $outlet->load('admin.user');
        $admins = Admin::with('user')->whereHas('user', fn($q) => $q->where('is_active', true))
            ->get()->sortBy('user.name');
            
        return view('superadmin.outlets.edit', compact('outlet', 'admins'));
    }

    /**
     * Memperbarui data outlet.
     */
    public function update(Request $request, Outlet $outlet)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'nullable|email|max:100|unique:outlets,email,' . $outlet->id,
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
            'province'  => 'nullable|string|max:50',
            'city'      => 'nullable|string|max:50',
            'admin_id'  => 'nullable|exists:admins,id',
            'is_active' => 'boolean'
        ]);

        $outlet->update($validated);

        // Update juga status user terkait jika ada
        if ($outlet->user) {
            $outlet->user->update(['is_active' => $validated['is_active']]);
        }
        
        return redirect()->route('outlets.index')->with('success', "Data outlet {$outlet->name} berhasil diperbarui.");
    }

    /**
     * Menghapus outlet.
     */
    public function destroy(Outlet $outlet)
    {
        try {
            DB::beginTransaction();
            // Hapus juga user terkait jika ada
            if ($outlet->user) {
                $outlet->user->delete();
            }
            $outlet->delete();
            DB::commit();
            return redirect()->route('outlets.index')->with('success', "Outlet {$outlet->name} berhasil dihapus permanen.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus outlet: ' . $e->getMessage());
            return back()->with('error', 'Gagal menghapus outlet.');
        }
    }

    /**
     * Mengubah status aktif/nonaktif outlet.
     */
    public function toggle(Outlet $outlet)
    {
        $newStatus = !$outlet->is_active;
        $outlet->update(['is_active' => $newStatus]);

        // Update juga status user terkait
        if($outlet->user) {
            $outlet->user->update(['is_active' => $newStatus]);
        }

        $statusText = $newStatus ? 'diaktifkan' : 'dinonaktifkan';
        return back()->with('success', "Outlet {$outlet->name} dan akun user terkait berhasil {$statusText}.");
    }

    /**
     * Reset password user outlet.
     */
    public function resetPassword(Outlet $outlet)
    {
        // Cari user berdasarkan email dari outlet, atau user_id jika ada relasi
        $user = $outlet->user ?? User::where('email', $outlet->email)->first();

        if (!$user) {
            return back()->with('error', 'User untuk outlet ini tidak ditemukan.');
        }

        $user->update(['password' => Hash::make('default123')]);

        return back()->with('success', "Password untuk user {$user->email} berhasil direset.");
    }
}