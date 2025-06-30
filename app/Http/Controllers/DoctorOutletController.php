<?php

namespace App\Http\Controllers;

use App\Models\Doctor;
use App\Models\Outlet;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Str;

class DoctorOutletController extends Controller
{
  public function index(Request $request)
{
    $user = auth()->user();
    $outlet = Outlet::where('user_id', $user->id)->first();

    if (!$outlet) {
        return redirect()->route('dashboard')->with('error', 'Outlet tidak ditemukan untuk akun ini.');
    }

    $doctors = Doctor::with(['user'])
        ->withCount(['results' => function ($query) use ($outlet) {
            $query->where('outlet_id', $outlet->id);
        }])
        ->where('outlet_id', $outlet->id)
        ->when($request->filled('search'), function ($query) use ($request) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', "%{$request->search}%")
                  ->orWhere('email', 'like', "%{$request->search}%");
            });
        })
        ->latest()
        ->paginate(10)
        ->appends($request->query());

    return view('outlets.doctors.index', compact('doctors'));
}

    public function create()
    {
        return view('outlets.doctors.create');
    }

    public function store(Request $request)
    {
        $user = auth()->user();
        $outlet = Outlet::where('user_id', $user->id)->first();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'phone' => 'nullable|string|max:20',
            'password' => 'required|string|min:6',
            'signature_image' => 'nullable|string', // base64 image
            'gender' => 'nullable|in:male,female',
            'birth_date' => 'nullable|date',
            'specialist' => 'nullable|string|max:100',
            'education' => 'nullable|string|max:100',
            'license_number' => 'nullable|string|max:50',
            'practice_days' => 'nullable|string|max:100',
            'specialization' => 'nullable|string|max:100',

        ]);

        // Buat akun user
        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'phone' => $validated['phone'] ?? null,
            'role_type' => 'doctor',
            
        ]);

        // Siapkan signature image & QR code
        $signatureImagePath = null;
        $qrCodePath = null;
        $signatureToken = Str::uuid()->toString();

        if ($request->filled('signature_image')) {
            // Simpan signature image dari base64
            $base64 = $request->input('signature_image');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            $signatureImagePath = "signatures/doctor-{$user->id}.png";
            Storage::disk('public')->put($signatureImagePath, $imageData);

            // Generate QR Code
            $qrPayload = "Dokter: {$validated['name']}\nEmail: {$validated['email']}\nToken: {$signatureToken}";
            $qrCodeImage = QrCode::format('png')->size(250)->generate($qrPayload);
            $qrCodePath = "signatures/qrcode-doctor-{$user->id}.png";
            Storage::disk('public')->put($qrCodePath, $qrCodeImage);
        }

        // Simpan data dokter
        Doctor::create([
            'user_id' => $user->id,
            'outlet_id' => $outlet->id,
            'admin_id' => $outlet->admin_id,
            'phone' => $validated['phone'] ?? null,
            'signature_image' => $signatureImagePath,
            'signature_qrcode' => $qrCodePath,
            'signature_token' => $signatureToken,
            'signed_at' => now(),
            'is_signature_verified' => true,
            'specialist' => $validated['specialist'],
            'education' => $validated['education'],
            'license_number' => $validated['license_number'],
            'practice_days' => $validated['practice_days'],
            'specialization' => $validated['specialization'],
            'gender' => $validated['gender'],
            'birth_date' => $validated['birth_date'] ? \Carbon\Carbon::parse($validated['birth_date']) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('outlet.doctors.index')->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Doctor $doctor)
    {
        $this->authorizeDoctor($doctor);
        return view('outlets.doctors.edit', compact('doctor'));
    }

    public function update(Request $request, Doctor $doctor)
    {
        $this->authorizeDoctor($doctor);

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'phone' => 'nullable|string|max:20',
            'signature_image' => 'nullable|string',
        ]);

        // Update user
        $doctor->user->update([
            'name' => $validated['name'],
            'phone' => $validated['phone'],
        ]);

        // Perbarui signature (jika ada input baru)
        if ($request->filled('signature_image')) {
            $base64 = $request->input('signature_image');
            $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64));
            $signatureImagePath = "signatures/doctor-{$doctor->user_id}.png";
            Storage::disk('public')->put($signatureImagePath, $imageData);

            // Buat QR baru
            $token = Str::uuid()->toString();
            $qrPayload = "Dokter: {$doctor->user->name}\nEmail: {$doctor->user->email}\nToken: {$token}";
            $qrCodePath = "signatures/qrcode-doctor-{$doctor->user_id}.png";
            $qrCodeImage = QrCode::format('png')->size(250)->generate($qrPayload);
            Storage::disk('public')->put($qrCodePath, $qrCodeImage);

            $doctor->update([
                'signature_image' => $signatureImagePath,
                'signature_qrcode' => $qrCodePath,
                'signature_token' => $token,
                'signed_at' => now(),
                'is_signature_verified' => true,
            ]);
        }

        return redirect()->route('outlet.doctors.index')->with('success', 'Data dokter diperbarui.');
    }

    public function destroy(Doctor $doctor)
    {
        $this->authorizeDoctor($doctor);

        $doctor->user()->delete(); // Soft delete user
        $doctor->delete();

        return redirect()->route('outlet.doctors.index')->with('success', 'Dokter dihapus.');
    }

    protected function authorizeDoctor(Doctor $doctor)
    {
        $user = auth()->user();

        if ($user->role_type === 'outlet') {
            $outlet = Outlet::where('user_id', $user->id)->first();

            if (!$outlet) {
                abort(403, 'Outlet tidak ditemukan untuk akun ini.');
            }

            if ($doctor->outlet_id !== $outlet->id) {
                abort(403, 'Dokter ini tidak termasuk outlet Anda.');
            }
        }

        if ($user->role_type === 'admin') {
            $admin = $user->admin;

            if (!$admin) {
                abort(403, 'Admin tidak valid.');
            }

            $allowedOutletIds = Outlet::where('admin_id', $admin->id)->pluck('id')->toArray();

            if (!in_array($doctor->outlet_id, $allowedOutletIds)) {
                abort(403, 'Dokter ini bukan bagian dari outlet Anda.');
            }
        }
    }
}
