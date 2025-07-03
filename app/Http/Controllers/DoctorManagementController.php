<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Result;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB; // TAMBAHKAN: untuk DB facade

class DoctorManagementController extends Controller
{
    /**
     * Menampilkan daftar dokter.
     */
    public function index(Request $request)
    {
        // UBAH: Optimalkan query untuk performa yang lebih baik
        $query = Doctor::with(['user', 'outlet'])
            ->withCount('results as result_count') // Gunakan withCount untuk efisiensi
            ->when($request->search, function ($q) use ($request) {
                $q->where(function ($sub) use ($request) {
                    $sub->whereHas('user', function ($u) use ($request) {
                        $u->where('name', 'like', "%{$request->search}%")
                          ->orWhere('email', 'like', "%{$request->search}%");
                    })->orWhere('specialist', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('is_active'), function ($q) use ($request) {
                $q->whereHas('user', fn($u) => $u->where('is_active', $request->is_active));
            });

        // TAMBAHKAN: Data untuk kartu statistik
        $totalDoctors = (clone $query)->count();
        $bannedDoctors = (clone $query)->whereHas('user', fn($u) => $u->where('is_active', false))->count();
        $totalLettersByDoctors = Result::whereIn('doctor_id', (clone $query)->pluck('id'))->count();
        
        $doctors = $query->latest('id')->paginate(10);

        // UBAH: Kirim semua data yang dibutuhkan ke view
        return view('superadmin.doctors.index', compact(
            'doctors', 
            'totalDoctors',
            'bannedDoctors',
            'totalLettersByDoctors'
        ));
    }

    /**
     * Menampilkan form untuk membuat dokter baru.
     * (Tidak ada perubahan)
     */
    public function create()
    {
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();
        return view('superadmin.doctors.create', compact('outlets'));
    }

    /**
     * Menyimpan dokter baru ke database.
     * UBAH: Gunakan DB Transaction untuk keamanan data
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email',
            'phone'          => 'nullable|string|max:20',
            'password'       => 'nullable|string|min:6',
            'specialist'     => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:50',
            'gender'         => 'nullable|in:male,female',
            'birth_date'     => 'nullable|date',
            'outlet_id'      => 'nullable|exists:outlets,id',
        ]);

        try {
            DB::beginTransaction();

            $user = User::create([
                'name'      => $validated['name'],
                'email'     => $validated['email'],
                'phone'     => $validated['phone'],
                'password'  => Hash::make($validated['password'] ?? 'doctor123'),
                'role_type' => 'doctor',
                'is_active' => true,
            ]);

            $user->doctor()->create([
                'outlet_id'      => $validated['outlet_id'],
                'specialist'     => $validated['specialist'],
                'license_number' => $validated['license_number'],
                'gender'         => $validated['gender'],
                'birth_date'     => $validated['birth_date'],
            ]);
            
            DB::commit();

            return redirect()->route('doctors.index')->with('success', 'Dokter baru berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menambahkan dokter: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan form untuk mengedit dokter.
     * (Tidak ada perubahan)
     */
    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        $outlets = Outlet::where('is_active', true)->orderBy('name')->get();

        return view('superadmin.doctors.edit', compact('doctor', 'outlets'));
    }

    /**
     * Memperbarui data dokter di database.
     * UBAH: Gunakan DB Transaction
     */
    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name'           => 'required|string|max:100',
            'email'          => 'required|email|unique:users,email,' . $doctor->user_id,
            'phone'          => 'nullable|string|max:20',
            'specialist'     => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:50',
            'gender'         => 'nullable|in:male,female',
            'birth_date'     => 'nullable|date',
            'outlet_id'      => 'nullable|exists:outlets,id',
        ]);

        try {
            DB::beginTransaction();

            $doctor->user->update([
                'name'  => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
            ]);

            $doctor->update([
                'outlet_id'      => $validated['outlet_id'],
                'specialist'     => $validated['specialist'],
                'license_number' => $validated['license_number'],
                'gender'         => $validated['gender'],
                'birth_date'     => $validated['birth_date'],
            ]);

            DB::commit();
            
            return redirect()->route('doctors.index')->with('success', "Data dokter {$doctor->user->name} berhasil diperbarui.");
        
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memperbarui data dokter: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menghapus dokter dari database.
     * UBAH: Gunakan DB Transaction
     */
    public function destroy(Doctor $doctor)
    {
        try {
            DB::beginTransaction();
            // Hapus relasi dokter terlebih dahulu
            $doctor->delete();
            // Hapus user yang terkait
            $doctor->user()->delete();
            DB::commit();
            
            return redirect()->route('doctors.index')->with('success', 'Dokter berhasil dihapus permanen.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal menghapus dokter: ' . $e->getMessage());
        }
    }

    /**
     * Toggle status aktif/nonaktif user dokter.
     * (Tidak ada perubahan signifikan)
     */
    public function ban(User $user)
    {
        if ($user->role_type !== 'doctor') {
            abort(403, 'Hanya akun dokter yang bisa diubah statusnya.');
        }

        $user->update(['is_active' => false]);

        return redirect()->route('doctors.index')->with('success', "Akun dokter {$user->name} telah dinonaktifkan.");
    }

    /**
     * Mengaktifkan kembali user dokter.
     * (Tidak ada perubahan signifikan)
     */
    public function unban(User $user)
    {
        if ($user->role_type !== 'doctor') {
            abort(403, 'Hanya akun dokter yang bisa diubah statusnya.');
        }

        $user->update(['is_active' => true]);

        return redirect()->route('doctors.index')->with('success', "Akun dokter {$user->name} telah diaktifkan kembali.");
    }

    /**
     * Reset password user dokter.
     * (Tidak ada perubahan signifikan)
     */
    public function resetPassword(User $user)
    {
        if ($user->role_type !== 'doctor') {
            abort(403, 'Akun bukan dokter.');
        }

        $user->password = Hash::make('doctor123');
        $user->save();

        return redirect()->route('doctors.index')->with('success', "Password dokter {$user->name} berhasil direset ke 'doctor123'.");
    }
}