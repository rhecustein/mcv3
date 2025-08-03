<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\SessionLogin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

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
     * Handle an incoming authentication request with enhanced session management.
     */
    public function store(Request $request): RedirectResponse
    {
        // Validate request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'force_login' => 'nullable|boolean', // For force login option
        ]);

        $credentials = [
            'email' => $request->email,
            'password' => $request->password,
        ];

        // Enhanced rate limiting with IP and email
        $rateLimitKey = 'login:' . md5($request->email . $request->ip());
        $globalRateLimitKey = 'global_login:' . $request->ip();

        // Check rate limits
        if (RateLimiter::tooManyAttempts($rateLimitKey, 5)) {
            $seconds = RateLimiter::availableIn($rateLimitKey);
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login untuk email ini. Coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
            ]);
        }

        if (RateLimiter::tooManyAttempts($globalRateLimitKey, 10)) {
            $seconds = RateLimiter::availableIn($globalRateLimitKey);
            throw ValidationException::withMessages([
                'email' => 'Terlalu banyak percobaan login dari IP ini. Coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
            ]);
        }

        // Attempt authentication
        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($rateLimitKey, 900); // 15 minutes
            RateLimiter::hit($globalRateLimitKey, 1800); // 30 minutes

            // Log failed attempt
            Log::warning('Failed login attempt', [
                'email' => $request->email,
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'Email atau password tidak valid.',
            ]);
        }

        // Success - get user
        $user = Auth::user();
        
        // Check if user is active
        if (isset($user->is_active) && !$user->is_active) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        // Handle session management
        $sessionResult = $this->handleSessionManagement($user, $request);
        
        if ($sessionResult['action'] === 'force_logout') {
            Auth::logout();
            
            return redirect()->route('login')
                ->with('warning', 'Anda sudah login di device lain. Gunakan "Force Login" jika ingin menggantikan sesi tersebut.')
                ->with('show_force_login', true)
                ->withInput(['email' => $request->email]);
        }

        // Clear rate limiting and regenerate session
        RateLimiter::clear($rateLimitKey);
        RateLimiter::clear($globalRateLimitKey);
        $request->session()->regenerate();

        // Create session record
        $this->createSessionRecord($user, $request, $sessionResult['session_id']);

        // Log successful login
        $this->logSuccessfulLogin($user, $request, $sessionResult);

        // Update user last login
        $this->updateUserLastLogin($user, $request);

        // Redirect based on role
        return $this->redirectByRole($user);
    }

    /**
     * Handle session management logic
     */
    private function handleSessionManagement($user, $request): array
    {
        $sessionId = session()->getId();
        $forceLogin = $request->boolean('force_login');
        
        // Check for existing active sessions
        $existingSessions = SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('session_id', '!=', $sessionId)
            ->get();

        // If no existing sessions, proceed normally
        if ($existingSessions->isEmpty()) {
            return [
                'action' => 'allow',
                'session_id' => $sessionId,
                'message' => 'Login berhasil'
            ];
        }

        // Check if single session is enforced for this user/role
        $enforceSingleSession = $this->shouldEnforceSingleSession($user);

        if (!$enforceSingleSession) {
            return [
                'action' => 'allow',
                'session_id' => $sessionId,
                'message' => 'Multiple sessions allowed'
            ];
        }

        // If force login is requested, invalidate other sessions
        if ($forceLogin) {
            $this->invalidateOtherSessions($user, $sessionId);
            
            Log::info('Force login - other sessions invalidated', [
                'user_id' => $user->id,
                'new_session_id' => $sessionId,
                'invalidated_sessions' => $existingSessions->pluck('session_id')->toArray()
            ]);

            return [
                'action' => 'allow',
                'session_id' => $sessionId,
                'message' => 'Force login berhasil',
                'invalidated_sessions' => $existingSessions->count()
            ];
        }

        // Default: force logout for single session enforcement
        return [
            'action' => 'force_logout',
            'session_id' => $sessionId,
            'message' => 'Single session enforced',
            'existing_sessions' => $existingSessions->count()
        ];
    }

    /**
     * Check if single session should be enforced for user
     */
    private function shouldEnforceSingleSession($user): bool
    {
        // Skip enforcement in development
        if (app()->environment('local')) {
            return false;
        }

        // Skip for certain roles
        $exemptRoles = ['superadmin', 'developer'];
        if (in_array($user->role_type, $exemptRoles)) {
            return false;
        }

        // Check user preference if column exists
        if (isset($user->allow_multiple_sessions) && $user->allow_multiple_sessions) {
            return false;
        }

        // Default: enforce single session
        return true;
    }

    /**
     * Invalidate other active sessions for user
     */
    private function invalidateOtherSessions($user, $currentSessionId): void
    {
        try {
            // Mark other sessions as inactive
            SessionLogin::where('user_id', $user->id)
                ->where('session_id', '!=', $currentSessionId)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                    'logout_reason' => 'force_login_from_another_device'
                ]);

            // Also clear from Laravel sessions table if using database driver
            if (config('session.driver') === 'database') {
                DB::table(config('session.table', 'sessions'))
                    ->whereIn('id', function($query) use ($user, $currentSessionId) {
                        $query->select('session_id')
                              ->from('session_logins')
                              ->where('user_id', $user->id)
                              ->where('session_id', '!=', $currentSessionId)
                              ->where('logout_reason', 'force_login_from_another_device');
                    })
                    ->delete();
            }

        } catch (\Exception $e) {
            Log::error('Error invalidating other sessions', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Create session record
     */
    private function createSessionRecord($user, $request, $sessionId): void
    {
        try {
            // Check if SessionLogin model exists and table is available
            if (class_exists('App\Models\SessionLogin')) {
                SessionLogin::create([
                    'user_id' => $user->id,
                    'session_id' => $sessionId,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                    'logged_in_at' => now(),
                    'is_active' => true,
                    'last_activity' => now(),
                ]);
            }
        } catch (\Exception $e) {
            Log::warning('Could not create session record', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Log successful login with enhanced details
     */
    private function logSuccessfulLogin($user, $request, $sessionResult): void
    {
        try {
            Log::info('User login success', [
                'user_id' => $user->id,
                'email' => $user->email,
                'role' => $user->role_type ?? 'unknown',
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'session_id' => session()->getId(),
                'session_action' => $sessionResult['action'],
                'invalidated_sessions' => $sessionResult['invalidated_sessions'] ?? 0,
                'timestamp' => now(),
                'remember_me' => $request->boolean('remember'),
            ]);
        } catch (\Exception $e) {
            // Ignore logging errors but try to log the error itself
            Log::error('Login logging failed', ['error' => $e->getMessage()]);
        }
    }

    /**
     * Update user last login information
     */
    private function updateUserLastLogin($user, $request): void
    {
        try {
            $user->update([
                'last_login_at' => now(),
                'last_login_ip' => $request->ip(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Could not update user last login', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Redirect user based on their role with enhanced logic.
     */
    private function redirectByRole($user): RedirectResponse
    {
        $roleType = $user->role_type ?? 'default';

        // Log the redirect attempt
        Log::info('Redirecting user by role', [
            'user_id' => $user->id,
            'role' => $roleType,
        ]);

        // Check for intended URL first (but only if it's safe)
        $intended = session()->pull('url.intended');
        if ($intended && $this->isSafeRedirectUrl($intended, $roleType)) {
            return redirect($intended);
        }

        // Direct redirect based on role
        try {
            $url = $this->getDefaultUrl($roleType);
            return redirect($url);
        } catch (\Exception $e) {
            Log::error('Role redirect failed', [
                'user_id' => $user->id,
                'role' => $roleType,
                'error' => $e->getMessage()
            ]);
            
            // Fallback to dashboard
            return redirect('/dashboard');
        }
    }

    /**
     * Check if redirect URL is safe for the user's role
     */
    private function isSafeRedirectUrl($url, $roleType): bool
    {
        // Parse URL to check path
        $path = parse_url($url, PHP_URL_PATH);
        
        // Define allowed paths for each role
        $allowedPaths = [
            'superadmin' => ['/superadmin/', '/dashboard'],
            'admin' => ['/admin/', '/dashboard'],
            'outlet' => ['/outlet/', '/dashboard'],
            'doctor' => ['/doctor/', '/dashboard'],
            'company' => ['/company/', '/dashboard'],
            'companies' => ['/company/', '/dashboard'],
            'patient' => ['/patient/', '/dashboard'],
        ];

        $userAllowedPaths = $allowedPaths[$roleType] ?? ['/dashboard'];
        
        foreach ($userAllowedPaths as $allowedPath) {
            if (str_starts_with($path, $allowedPath)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get default URL for each role with fallback.
     */
    private function getDefaultUrl(string $roleType): string
    {
        // Use route names that match our routes
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
            // Fallback to URL if route doesn't exist
            Log::warning('Route not found for role, using URL fallback', [
                'role' => $roleType,
                'error' => $e->getMessage()
            ]);
            
            switch ($roleType) {
                case 'superadmin':
                    return '/superadmin/dashboard';
                case 'admin':
                    return '/admin/dashboard';
                case 'outlet':
                    return '/outlet/dashboard';
                case 'doctor':
                    return '/doctor/dashboard';
                case 'companies':
                case 'company':
                    return '/company/dashboard';
                case 'patient':
                    return '/patient/dashboard';
                default:
                    return '/dashboard';
            }
        }
    }

    /**
     * Enhanced logout with session cleanup.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        $sessionId = session()->getId();
        
        // Log logout attempt
        if ($user) {
            try {
                Log::info('User logout', [
                    'user_id' => $user->id,
                    'email' => $user->email,
                    'session_id' => $sessionId,
                    'ip' => $request->ip(),
                ]);

                // Update session record
                if (class_exists('App\Models\SessionLogin')) {
                    SessionLogin::where('user_id', $user->id)
                        ->where('session_id', $sessionId)
                        ->update([
                            'is_active' => false,
                            'logged_out_at' => now(),
                            'logout_reason' => 'manual_logout'
                        ]);
                }

            } catch (\Exception $e) {
                Log::error('Logout logging failed', ['error' => $e->getMessage()]);
            }
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil logout.');
    }

    /**
     * Force logout other sessions (API endpoint)
     */
    public function forceLogoutOtherSessions(Request $request)
    {
        $user = Auth::user();
        $currentSessionId = session()->getId();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        try {
            $this->invalidateOtherSessions($user, $currentSessionId);

            return response()->json([
                'message' => 'Sesi lain berhasil diputuskan',
                'current_session' => $currentSessionId
            ]);

        } catch (\Exception $e) {
            Log::error('Force logout other sessions failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Gagal memutuskan sesi lain'], 500);
        }
    }

    /**
     * Get active sessions for current user
     */
    public function getActiveSessions(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthenticated'], 401);
        }

        try {
            if (!class_exists('App\Models\SessionLogin')) {
                return response()->json(['sessions' => []]);
            }

            $sessions = SessionLogin::where('user_id', $user->id)
                ->where('is_active', true)
                ->orderBy('last_activity', 'desc')
                ->get()
                ->map(function ($session) {
                    return [
                        'id' => $session->id,
                        'session_id' => substr($session->session_id, 0, 8) . '...',
                        'ip_address' => $session->ip_address,
                        'user_agent' => $session->user_agent,
                        'logged_in_at' => $session->logged_in_at,
                        'last_activity' => $session->last_activity,
                        'is_current' => $session->session_id === session()->getId(),
                    ];
                });

            return response()->json(['sessions' => $sessions]);

        } catch (\Exception $e) {
            Log::error('Get active sessions failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Gagal mengambil data sesi'], 500);
        }
    }

    /**
     * Check session status (for heartbeat)
     */
    public function checkSession(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['authenticated' => false], 401);
        }

        try {
            // Update last activity if session record exists
            if (class_exists('App\Models\SessionLogin')) {
                SessionLogin::where('user_id', $user->id)
                    ->where('session_id', session()->getId())
                    ->update(['last_activity' => now()]);
            }

            return response()->json([
                'authenticated' => true,
                'user' => [
                    'id' => $user->id,
                    'email' => $user->email,
                    'role' => $user->role_type,
                ],
                'session_id' => substr(session()->getId(), 0, 8) . '...'
            ]);

        } catch (\Exception $e) {
            Log::error('Session check failed', [
                'user_id' => $user->id,
                'error' => $e->getMessage()
            ]);

            return response()->json(['authenticated' => true]); // Fallback
        }
    }
}