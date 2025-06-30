<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\User;
use App\Models\Outlet;
use App\Models\Result;
use Illuminate\Http\Request; 
use Illuminate\Support\Facades\Hash;

class DoctorManagementController extends Controller
{
    public function index(Request $request)
    {
        $doctors = Doctor::with(['user', 'outlet'])
            ->when($request->search, fn($q) =>
                $q->whereHas('user', fn($u) =>
                    $u->where('name', 'like', "%{$request->search}%")
                      ->orWhere('email', 'like', "%{$request->search}%")
                )->orWhere('specialist', 'like', "%{$request->search}%")
            )
            ->when($request->is_active !== null, fn($q) =>
                $q->whereHas('user', fn($u) =>
                    $u->where('is_active', $request->is_active)
                )
            )
            ->latest()
            ->paginate(15);

        // Hitung jumlah hasil surat per dokter
        foreach ($doctors as $doctor) {
            $doctor->result_count = Result::where('doctor_id', $doctor->id)->count();
        }

        $totalDoctors = Doctor::count();

        return view('superadmin.doctors.index', compact('doctors', 'totalDoctors'));
    }

    public function create()
    {
        $outlets = Outlet::all();
        return view('superadmin.doctors.create', compact('outlets'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users',
            'phone'      => 'nullable|string|max:20',
            'password'   => 'nullable|string|min:6',
            'specialist' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:50',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'outlet_id'  => 'nullable|exists:outlets,id',
        ]);

        $user = User::create([
            'name'      => $validated['name'],
            'email'     => $validated['email'],
            'phone'     => $validated['phone'],
            'password'  => Hash::make($validated['password'] ?? 'doctor123'),
            'role_type' => 'doctor',
            'is_active' => true,
        ]);

        Doctor::create([
            'user_id'        => $user->id,
            'outlet_id'      => $validated['outlet_id'],
            'specialist'     => $validated['specialist'],
            'license_number' => $validated['license_number'],
            'gender'         => $validated['gender'],
            'birth_date'     => $validated['birth_date'],
        ]);

        return redirect()->route('doctors.index')->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Doctor $doctor)
    {
        $doctor->load('user');
        $outlets = Outlet::all();

        return view('superadmin.doctors.edit', compact('doctor', 'outlets'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $validated = $request->validate([
            'name'       => 'required|string|max:100',
            'email'      => 'required|email|unique:users,email,' . $doctor->user_id,
            'phone'      => 'nullable|string|max:20',
            'specialist' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:50',
            'gender'     => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'outlet_id'  => 'nullable|exists:outlets,id',
        ]);

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

        return redirect()->route('doctors.index')->with('success', 'Data dokter berhasil diperbarui.');
    }

    public function destroy(Doctor $doctor)
    {
        $doctor->user()->delete();
        $doctor->delete();

        return redirect()->route('doctors.index')->with('success', 'Dokter berhasil dihapus.');
    }

    public function ban(User $doctor)
    {
        if ($doctor->role_type !== 'doctor') {
            abort(403, 'Hanya akun dokter yang bisa diblokir.');
        }

        $doctor->is_active = false;
        $doctor->save();

        return redirect()->route('doctors.index')->with('success', 'Akun dokter telah dinonaktifkan.');
    }

    public function unban(User $doctor)
    {
        if ($doctor->role_type !== 'doctor') {
            abort(403, 'Hanya akun dokter yang bisa diaktifkan.');
        }

        $doctor->is_active = true;
        $doctor->save();

        return redirect()->route('doctors.index')->with('success', 'Akun dokter telah diaktifkan kembali.');
    }

    public function resetPassword(User $doctor)
    {
        if ($doctor->role_type !== 'doctor') {
            abort(403, 'Akun bukan dokter.');
        }

        $doctor->password = Hash::make('doctor123');
        $doctor->save();

        return redirect()->route('doctors.index')->with('success', 'Password dokter berhasil direset ke "doctor123".');
    }
}
