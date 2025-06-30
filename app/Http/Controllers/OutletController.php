<?php

namespace App\Http\Controllers;

use App\Models\Outlet;
use App\Models\Admin;
use App\Models\Doctor;
use App\Models\Result;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Carbon\Carbon;
use DB;
use Illuminate\Support\Facades\Auth;

class OutletController extends Controller
{
    /**
     * 📊 Dashboard outlet untuk role `outlet`
     */
   public function dashboard()
    {
        $user = auth()->user();

        // Cari outlet berdasarkan email user
        $outlet = Outlet::where('email', $user->email)->first();

        if (!$outlet) {
            abort(403, 'Outlet tidak ditemukan.');
        }

        $totalDoctors = Doctor::where('outlet_id', $outlet->id)->count();
        $totalLetters = Result::where('outlet_id', $outlet->id)->count();

        $latestLetters = Result::with(['patient', 'doctor.user'])
            ->where('outlet_id', $outlet->id)
            ->latest()
            ->take(5)
            ->get();

        // Ambil data jumlah surat per bulan
        $monthlyLetters = DB::table('results')
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, COUNT(*) as total")
            ->where('outlet_id', $outlet->id)
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // Format untuk chart
        $labels = [];
        $data = [];

        foreach ($monthlyLetters as $entry) {
            $labels[] = Carbon::parse($entry->month . '-01')->translatedFormat('F Y');
            $data[] = $entry->total;
        }

        return view('outlets.dashboard', compact(
            'outlet',
            'totalDoctors',
            'totalLetters',
            'latestLetters',
            'labels',
            'data'
        ));
    }

    /**
     * 🗂️ Daftar semua outlet (superadmin only)
     */
    public function index(Request $request)
    {
        $outlets = Outlet::with(['admin.user'])
            ->withCount([
                'doctors as doctor_count',
                'results as letter_count'
            ])
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->where('name', 'like', "%{$request->search}%")
                        ->orWhere('email', 'like', "%{$request->search}%")
                        ->orWhere('city', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('is_active'), fn($q) =>
                $q->where('is_active', $request->is_active)
            )
            ->latest()
            ->paginate(15);

        $totalOutlets = Outlet::count();
        $bannedOutlets = Outlet::where('is_active', false)->count();

        return view('superadmin.outlets.index', compact(
            'outlets',
            'totalOutlets',
            'bannedOutlets'
        ));
    }


    /**
     * ➕ Form tambah outlet (superadmin only)
     */
    public function create()
    {
        $admins = Admin::with('user')->get();
        return view('superadmin.outlets.create', compact('admins'));
    }

    /**
     * 💾 Simpan outlet baru (superadmin only)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'      => 'required|string|max:100',
            'email'     => 'nullable|email|max:100|unique:outlets',
            'phone'     => 'nullable|string|max:20',
            'address'   => 'nullable|string|max:255',
            'province'  => 'nullable|string|max:50',
            'city'      => 'nullable|string|max:50',
            'admin_id'  => 'nullable|exists:admins,id',
        ]);

        Outlet::create($validated);

        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil ditambahkan.');
    }

    /**
     * ✏️ Form edit outlet (superadmin only)
     */
    public function edit(Outlet $outlet)
    {
        $admins = Admin::with('user')->get();
        return view('superadmin.outlets.edit', compact('outlet', 'admins'));
    }

    /**
     * 🔄 Update outlet (superadmin only)
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
            'password'  => 'nullable|string|min:6',
        ]);

        // Simpan perubahan outlet
        $outlet->update(collect($validated)->except('password')->toArray());

        // Ubah password admin jika diisi dan admin ada
        if ($request->filled('password') && $outlet->admin && $outlet->admin->user) {
            $outlet->admin->user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil diperbarui.');
    }


    /**
     * 🗑️ Hapus outlet (soft delete)
     */
    public function destroy(Outlet $outlet)
    {
        $outlet->delete();
        return redirect()->route('outlets.index')->with('success', 'Outlet berhasil dihapus.');
    }

    public function toggleActive(Outlet $outlet)
    {
        $outlet->update(['is_active' => !$outlet->is_active]);
        return back()->with('success', 'Status outlet berhasil diperbarui.');
    }

    public function resetPassword(Outlet $outlet)
    {
        // Ambil user berdasarkan email dari outlet
        $user = User::where('email', $outlet->email)->first();

        if (!$user) {
            return back()->with('error', 'User dengan email outlet tidak ditemukan.');
        }

        // Set password baru
        $user->forceFill([
            'password' => Hash::make('default123'),
        ])->save();

        // Verifikasi hash berhasil
        $check = Hash::check('default123', $user->fresh()->password);
        Log::info('✅ Reset Password Check:', [
            'outlet' => $outlet->name,
            'user_email' => $user->email,
            'check' => $check,
        ]);

        if (!$check) {
            return back()->with('error', 'Password gagal disimpan.');
        }

        return back()->with('success', "Password user <strong>{$user->email}</strong> berhasil direset ke <code>default123</code>.");
    }

}
