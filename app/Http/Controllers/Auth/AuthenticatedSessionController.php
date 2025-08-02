<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Show the login form.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Simple rate limiting
        $rateLimitKey = 'login:' . md5($request->email . $request->ip());

        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login. Coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
            ]);
        }

        // Attempt authentication
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($rateLimitKey, 900); // 15 minutes

            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak valid.',
            ]);
        }

        // Success - get user and redirect
        $user = Auth::user();
        
        // Clear rate limiting and regenerate session
        RateLimiter::clear($rateLimitKey);
        $request->session()->regenerate();

        // Simple logging
        try {
            Log::info('User login success', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role_type ?? 'unknown',
                'ip' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            // Ignore logging errors
        }

        // Redirect based on role
        return $this->redirectByRole($user);
    }

    /**
     * Redirect user based on their role.
     */
    private function redirectByRole($user): RedirectResponse
    {
        $roleType = $user->role_type ?? 'default';

        // Log the redirect attempt
        Log::info('Redirecting user by role', [
            'user_id' => $user->id,
            'role' => $roleType,
        ]);

        // Direct redirect without intended() to avoid issues
        switch ($roleType) {
            case 'superadmin':
                return redirect('/superadmin/dashboard');
            case 'admin':
                return redirect('/admin/dashboard');
            case 'outlet':
                return redirect('/outlet/dashboard');
            case 'doctor':
                return redirect('/doctor/dashboard');
            case 'companies':
            case 'company':
                return redirect('/company/dashboard');
            case 'patient':
                return redirect('/patient/dashboard');
            default:
                return redirect('/dashboard');
        }
    }

    /**
     * Get default URL for each role.
     */
    private function getDefaultUrl(string $roleType): string
    {
        // Use route names that match our fixed web.php
        try {
            switch ($roleType) {
                case 'superadmin':
                    return route('superadmin.dashboard');
                case 'admin':
                    return route('admin.dashboard');
                case 'outlet':
                    return route('outlet.dashboard');
                case 'doctor':
                    return route('doctor.dashboard');
                case 'companies':
                case 'company':
                    return route('company.dashboard');
                case 'patient':
                    return route('patient.dashboard');
                default:
                    return route('dashboard');
            }
        } catch (\Exception $e) {
            // Fallback to dashboard if route doesn't exist
            Log::warning('Route not found for role, using dashboard fallback', [
                'role' => $roleType,
                'error' => $e->getMessage()
            ]);
            return route('dashboard');
        }
    }

    /**
     * Log user logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        // Simple logout logging
        if ($user) {
            try {
                Log::info('User logout', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                ]);
            } catch (\Exception $e) {
                // Ignore logging errors
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}