<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\IpLock;
use App\Models\SessionLogin;
use App\Services\IP2LocationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (!$request->filled('latitude') || !$request->filled('longitude')) {
            throw ValidationException::withMessages([
                'email' => 'Akses lokasi diperlukan untuk login.',
            ]);
        }

        $email = Str::lower($request->email);
        $ip    = $request->ip();
        $key   = "{$email}|{$ip}";

        $user = \App\Models\User::where('email', $email)->first();

        if ($user && !$user->hasRoleType('superadmin')) {
            if (IpLock::active()->stillLocked()->forIp($ip)->exists()) {
                Log::warning('🔒 IP diblokir', ['ip' => $ip]);

                throw ValidationException::withMessages([
                    'email' => 'IP Anda diblokir sementara.',
                ]);
            }
        }

        if (!Auth::guard('web')->attempt([
            'email' => $email,
            'password' => $request->password
        ], $request->boolean('remember'))) {

            RateLimiter::hit($key);

            if ($user && RateLimiter::attempts($key) >= 5 && !$user->hasRoleType(['superadmin', 'outlet'])) {
                IpLock::create([
                    'ip_address'   => $ip,
                    'locked_until' => now()->addMinutes(30),
                    'locked_at'    => now(),
                    'reason'       => '5x gagal login',
                    'locked_by'    => null,
                    'is_active'    => true,
                ]);

                Log::alert('🚫 IP diblokir setelah gagal 5x', ['ip' => $ip]);
            }

            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        $request->session()->regenerate();
        RateLimiter::clear($key);
        $user = Auth::user();

        // Lokasi dari IP
        $locationService = new IP2LocationService();
        $ipLocation = $locationService->getLocation($ip);

        $latitude  = $request->input('latitude') ?? $ipLocation['latitude'];
        $longitude = $request->input('longitude') ?? $ipLocation['longitude'];
        $city      = $ipLocation['city'] ?? 'UNKNOWN';
        $province  = $ipLocation['province'] ?? 'UNKNOWN';

        if (!$user->hasRoleType(['superadmin', 'outlet']) && $province !== 'Kepulauan Riau') {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            Log::warning('⛔ Login ditolak: luar provinsi', [
                'user_id'  => $user->id,
                'provinsi' => $province,
                'ip'       => $ip,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Login hanya diperbolehkan dari provinsi Kepulauan Riau.',
            ]);
        }

        // Auto logout sesi idle > 30 menit
        SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('last_activity_at', '<', now()->subMinutes(30))
            ->update([
                'is_active'     => false,
                'logged_out_at' => now(),
            ]);

        // Jika sesi aktif >= 3, logout sesi tertua
        $activeSessions = SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_activity_at', 'asc')
            ->get();

        if ($activeSessions->count() >= 3) {
            $oldest = $activeSessions->first();
            $oldest->update([
                'is_active'     => false,
                'logged_out_at' => now(),
            ]);
        }

        // Simpan sesi baru
        SessionLogin::create([
            'user_id'          => $user->id,
            'session_id'       => session()->getId(),
            'ip_address'       => $ip,
            'user_agent'       => $request->userAgent(),
            'device'           => $request->header('User-Agent'),
            'city'             => $city,
            'province'         => $province,
            'latitude'         => $latitude,
            'longitude'        => $longitude,
            'success'          => true,
            'is_active'        => true,
            'logged_in_at'     => now(),
            'last_activity_at' => now(),
        ]);

        Log::info('✅ Login berhasil', [
            'user_id' => $user->id,
            'ip'      => $ip,
        ]);

        return redirect()->intended($this->redirectByRole($user->role_type));
    }

    protected function redirectByRole(string $role): string
    {
        return match ($role) {
            'superadmin' => route('admins.dashboard'),
            'admin'      => route('admin.dashboard'),
            'outlet'     => route('outlet.dashboard'),
            'doctor'     => route('doctor.dashboard'),
            'companies'  => route('company.dashboard'),
            'patient'    => route('patient.dashboard'),
            default      => route('dashboard'),
        };
    }

    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            SessionLogin::where('user_id', $user->id)
                ->where('session_id', session()->getId())
                ->where('is_active', true)
                ->update([
                    'is_active'     => false,
                    'logged_out_at' => now(),
                ]);
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
