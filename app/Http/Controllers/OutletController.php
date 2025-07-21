<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Outlet;
use App\Models\Result;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class OutletController extends Controller
{
    /**
     * Menampilkan dashboard untuk role 'outlet'.
     */
    public function dashboard()
    {
        $user = auth()->user();
        $outlet = $user->outlet; // Asumsi relasi one-to-one dari User ke Outlet

        if (!$outlet) {
            // Jika user outlet tidak memiliki relasi outlet, bisa logout atau tampilkan error
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda tidak terhubung dengan outlet manapun.');
        }

        $totalDoctors = $outlet->doctors()->count();
        $totalLetters = $outlet->results()->count();

        $latestLetters = $outlet->results()
            ->with(['patient.user', 'doctor.user'])
            ->latest()
            ->take(5)
            ->get();

        // Mengisi data bulan yang kosong agar grafik tidak putus
        $startDate = now()->subYear()->startOfMonth();
        $endDate = now()->endOfMonth();
        $dateRange = Carbon::parse($startDate)->monthsUntil($endDate);
        
        $monthlyLetters = $outlet->results()
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('created_at', '>=', $startDate)
            ->groupBy('month')
            ->orderBy('month', 'asc')
            ->get()
            ->keyBy('month');

        $labels = [];
        $data = [];

        foreach ($dateRange as $date) {
            $month = $date->format('Y-m');
            $labels[] = $date->translatedFormat('F Y');
            $data[] = $monthlyLetters->get($month)->total ?? 0;
        }

        return view('outlets.dashboard', compact('outlet', 'totalDoctors', 'totalLetters', 'latestLetters', 'labels', 'data'));
    }

    /**
     * Menampilkan daftar semua outlet untuk Superadmin.
     */
    public function index(Request $request)
    {
        $totalOutlets = Outlet::count();
        $bannedOutlets = Outlet::where('is_active', false)->count();
        $totalLetters = Result::count();
        
        $outletsQuery = Outlet::with(['admin.user', 'user'])
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
    
        $provinces = Outlet::select('province')
            ->whereNotNull('province')
            ->where('province', '!=', '')
            ->distinct()
            ->orderBy('province')
            ->pluck('province');

        $outlets = $outletsQuery->latest()->paginate(12); // Menampilkan 12 per halaman, cocok untuk grid 3 kolom

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
        $admins = Admin::with('user')->whereHas('user', fn($q) => $q->where('is_active', true))
            ->get()->sortBy('user.name');
        return view('superadmin.outlets.create', compact('admins'));
    }

    /**
     * Menyimpan outlet baru.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'nullable|email|max:100|unique:users,email|unique:outlets,email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'admin_id' => 'nullable|exists:admins,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        DB::beginTransaction();
        try {
            $user = null;
            // Buat user baru untuk outlet jika email diisi
            if (!empty($validated['email'])) {
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('outlet123'), // Password default yang lebih baik
                    'role_type' => 'outlet',
                    'is_active' => true,
                ]);
            }

            // Tambahkan user_id ke data outlet sebelum membuat
            $outletData = $validated;
            if ($user) {
                $outletData['user_id'] = $user->id;
            }

            Outlet::create($outletData);
            
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
        $outlet->load('admin.user', 'user');
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
            'name' => 'required|string|max:100',
            'email' => ['nullable', 'email', 'max:100', Rule::unique('outlets')->ignore($outlet->id), Rule::unique('users')->ignore($outlet->user_id)],
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:50',
            'city' => 'nullable|string|max:50',
            'postal_code' => 'nullable|string|max:10',
            'admin_id' => 'nullable|exists:admins,id',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
        ]);

        DB::beginTransaction();
        try {
            $outlet->update($validated);

            // Sinkronisasi data user terkait
            if ($outlet->user) {
                // Jika outlet sudah punya user, update datanya
                $outlet->user->update([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                ]);
            } elseif (!empty($validated['email'])) {
                // Jika outlet belum punya user tapi sekarang email diisi, buat user baru
                $user = User::create([
                    'name' => $validated['name'],
                    'email' => $validated['email'],
                    'password' => Hash::make('outlet123'),
                    'role_type' => 'outlet',
                    'is_active' => $outlet->is_active,
                ]);
                $outlet->update(['user_id' => $user->id]);
            }

            DB::commit();
            return redirect()->route('outlets.index')->with('success', "Data outlet {$outlet->name} berhasil diperbarui.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal memperbarui outlet {$outlet->id}: " . $e->getMessage());
            return back()->with('error', 'Gagal memperbarui data. Silakan coba lagi.')->withInput();
        }
    }

    /**
     * Menghapus outlet secara permanen.
     */
    public function destroy(Outlet $outlet)
    {
        DB::beginTransaction();
        try {
            // Hapus juga user terkait jika ada
            if ($outlet->user) {
                $outlet->user->delete();
            }
            $outlet->delete();
            
            DB::commit();
            return redirect()->route('outlets.index')->with('success', "Outlet {$outlet->name} berhasil dihapus permanen.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Gagal menghapus outlet {$outlet->id}: " . $e->getMessage());
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
     * Reset password user outlet ke default.
     */
    public function resetPassword(Outlet $outlet)
    {
        if (!$outlet->user) {
            return back()->with('error', 'User untuk outlet ini tidak ditemukan. Tidak dapat mereset password.');
        }

        $outlet->user->update(['password' => Hash::make('outlet123')]);

        return back()->with('success', "Password untuk user {$outlet->user->email} berhasil direset ke default.");
    }
}