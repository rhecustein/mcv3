<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Outlet;
use App\Models\Company;
use App\Models\User;
use App\Helpers\PasswordHelper;
use App\Http\Requests\Patient\StorePatientRequest;
use App\Http\Requests\Patient\UpdatePatientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PatientController extends Controller
{
    public function index()
{
    $user = auth()->user();

    // Validasi role
    if (!in_array($user->role_type, ['admin', 'outlet'])) {
        abort(403, 'Role Anda tidak memiliki akses ke data pasien.');
    }

    $patients = collect();
    $outlets = collect();

    // === ROLE: ADMIN ===
    if ($user->role_type === 'admin') {
        $admin = $user->admin;

        if (!$admin) {
            abort(403, 'Akun Anda tidak terdaftar sebagai admin.');
        }

        $outletIds = Outlet::where('admin_id', $admin->id)->pluck('id');
        $outlets = Outlet::whereIn('id', $outletIds)->get();

        $patientsQuery = Patient::with(['user', 'company', 'outlet', 'results'])
            ->whereIn('outlet_id', $outletIds)
            ->latest();
    }

    // === ROLE: OUTLET ===
    if ($user->role_type === 'outlet') {
        $outlet = Outlet::where('user_id', $user->id)->first();

        if (!$outlet) {
            abort(403, 'Akun outlet Anda belum terhubung ke data outlet.');
        }

        $outlets = collect([$outlet]);

        $patientsQuery = Patient::with(['user', 'company', 'outlet', 'results'])
            ->where('outlet_id', $outlet->id)
            ->latest();
    }

    // === Eksekusi Paginasi & Hitung Statistik ===
    $patients = $patientsQuery->paginate(20);
    $totalPatients = $patients->total();

    // Statistik menggunakan query untuk efisiensi (daripada collect/filter)
    $patientIds = $patients->pluck('id');

    $totalMale = Patient::whereIn('id', $patientIds)->where('gender', 'L')->count();
    $totalFemale = Patient::whereIn('id', $patientIds)->where('gender', 'P')->count();
    $totalCompanies = Patient::whereIn('id', $patientIds)->whereNotNull('company_id')->distinct('company_id')->count();

    return view('outlets.patients.index', compact(
        'patients', 'outlets',
        'totalPatients', 'totalMale', 'totalFemale', 'totalCompanies'
    ));
}


    public function create()
    {
        $admin = auth()->user()->admin;
        $outlets = Outlet::where('admin_id', $admin->id)->get();
        $companies = Company::all();

        return view('admin.patients.create', compact('outlets', 'companies'));
    }

    public function store(StorePatientRequest $request)
    {
        // Authorization and validation handled by FormRequest

        // Generate secure password
        $passwordData = PasswordHelper::generateForNewUser();

        // Create user with secure password
        $user = User::create([
            'name' => $request->full_name,
            'email' => 'auto_' . uniqid() . '@mail.local',
            'password' => $passwordData['password'],
            'role_type' => 'patient',
            'must_change_password' => $passwordData['must_change_password'],
        ]);

        Patient::create([
            'user_id'      => $user->id,
            'full_name'    => $request->full_name,
            'gender'       => $request->gender,
            'birth_date'   => $request->birth_date,
            'phone'        => $request->phone,
            'nik'          => $request->nik,
            'identity'     => $request->identity,
            'address'      => $request->address,
            'outlet_id'    => $request->outlet_id,
            'company_id'   => $request->company_id,
            'company_name' => $request->company_id ? Company::find($request->company_id)->name : null,
        ]);

        return redirect()->route('patients.index')->with('success', 'Pasien berhasil ditambahkan.');
    }

    public function show(Patient $patient)
    {
        // Authorization check using policy
        $this->authorize('view', $patient);

        return view('admin.patients.show', compact('patient'));
    }

    public function edit(Patient $patient)
    {
        // Authorization check using policy
        $this->authorize('update', $patient);

        $admin = auth()->user()->admin;
        $outlets = Outlet::where('admin_id', $admin->id)->get();
        $companies = Company::all();

        return view('admin.patients.edit', compact('patient', 'outlets', 'companies'));
    }

    public function update(UpdatePatientRequest $request, Patient $patient)
    {
        // Authorization and validation handled by FormRequest

        $patient->update([
            'full_name'    => $request->full_name,
            'gender'       => $request->gender,
            'birth_date'   => $request->birth_date,
            'phone'        => $request->phone,
            'nik'          => $request->nik,
            'identity'     => $request->identity,
            'address'      => $request->address,
            'outlet_id'    => $request->outlet_id,
            'company_id'   => $request->company_id,
            'company_name' => $request->company_id ? Company::find($request->company_id)->name : null,
        ]);

        $patient->user->update([
            'name' => $request->full_name,
        ]);

        return redirect()->route('patients.index')->with('success', 'Pasien berhasil diperbarui.');
    }

    public function destroy(Patient $patient)
    {
        // Authorization check using policy
        $this->authorize('delete', $patient);

        $patient->user?->delete();
        $patient->delete();

        return back()->with('success', 'Pasien berhasil dihapus.');
    }
}
