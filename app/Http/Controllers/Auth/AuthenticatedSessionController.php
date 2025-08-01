<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\IpLock;
use App\Models\SessionLogin;
use App\Models\User;
use App\Services\IP2LocationService;
use App\Services\SecurityService;
use App\Events\UserLoggedIn;
use App\Events\UserLoginFailed;
use App\Events\IpBlocked;
use App\Events\SuspiciousActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Carbon\Carbon;

class AuthenticatedSessionController extends Controller
{
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 30;
    private const MAX_ACTIVE_SESSIONS = 3;
    private const SESSION_TIMEOUT_MINUTES = 30;
    private const ALLOWED_PROVINCES = ['Kepulauan Riau'];
    private const SUSPICIOUS_ATTEMPTS_THRESHOLD = 3;

    private IP2LocationService $locationService;
    private SecurityService $securityService;

    public function __construct(
        IP2LocationService $locationService,
        SecurityService $securityService
    ) {
        $this->locationService = $locationService;
        $this->securityService = $securityService;
    }

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
        $this->validateLoginRequest($request);

        $credentials = $this->extractCredentials($request);
        $ip = $request->ip();
        $rateLimitKey = $this->getRateLimitKey($credentials['email'], $ip);
        $user = null;
        $location = [];

        try {
            // Get location data early for event dispatching
            $location = $this->getLocationData($request, $ip);
            
            // Find user for context
            $user = User::where('email', $credentials['email'])->first();

            // Check for IP blocks and rate limits
            $this->checkSecurityRestrictions($ip, $rateLimitKey, $credentials['email'], $user);

            // Attempt authentication
            $authenticatedUser = $this->attemptAuthentication($credentials, $request, $rateLimitKey, $user, $location);

            // Validate geographic restrictions
            $this->validateGeographicRestrictions($request, $ip, $authenticatedUser, $location);

            // Manage active sessions
            $this->manageActiveSessions($authenticatedUser);

            // Create new session record
            $this->createSessionRecord($authenticatedUser, $request, $ip, $location);

            // Clear rate limiting and regenerate session
            $this->finalizeSuccessfulLogin($request, $rateLimitKey);

            // Fire success event
            event(new UserLoggedIn($authenticatedUser, $ip, $location));

            // Log successful login
            $this->logSuccessfulLogin($authenticatedUser, $ip);

            return redirect()->intended($this->getRedirectUrl($authenticatedUser->role_type));

        } catch (ValidationException $e) {
            // Enhanced failed login event with comprehensive data
            $this->handleFailedLoginEvent($credentials['email'], $ip, $request, $location, $user, $e);
            throw $e;
            
        } catch (\Exception $e) {
            Log::error('Critical login system error', [
                'ip' => $ip,
                'email' => $credentials['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()->toISOString(),
            ]);

            // Fire suspicious activity event for system errors
            if ($user) {
                event(new SuspiciousActivity($user, $ip, 'system_error_during_login', [
                    'error' => $e->getMessage(),
                    'location' => $location,
                ]));
            }

            throw ValidationException::withMessages([
                'email' => 'Terjadi kesalahan sistem. Silakan coba lagi atau hubungi administrator.',
            ]);
        }
    }

    /**
     * Handle user logout.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        
        if ($user) {
            $this->terminateUserSession($user, session()->getId());
            $this->logUserLogout($user, $request->ip());
        }

        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Anda telah berhasil logout.');
    }

    /**
     * Validate the incoming login request.
     */
    private function validateLoginRequest(Request $request): void
    {
        $request->validate([
            'email'    => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', 'min:6'],
            'remember' => ['boolean'],
        ]);

        // Enhanced geolocation validation
        if (!$request->filled('latitude') || !$request->filled('longitude')) {
            Log::warning('🌍 Login attempted without geolocation', [
                'ip' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ]);

            throw ValidationException::withMessages([
                'email' => 'Akses lokasi diperlukan untuk keamanan login. Pastikan browser mengizinkan akses lokasi.',
            ]);
        }
    }

    /**
     * Extract and sanitize credentials.
     */
    private function extractCredentials(Request $request): array
    {
        return [
            'email' => Str::lower(trim($request->email)),
            'password' => $request->password,
            'remember' => $request->boolean('remember'),
        ];
    }

    /**
     * Generate rate limiting key.
     */
    private function getRateLimitKey(string $email, string $ip): string
    {
        return "login_attempts:{$email}|{$ip}";
    }

    /**
     * Get comprehensive location data.
     */
    private function getLocationData(Request $request, string $ip): array
    {
        $ipLocation = $this->locationService->getLocation($ip);
        
        return [
            'latitude'  => $request->input('latitude') ?? $ipLocation['latitude'],
            'longitude' => $request->input('longitude') ?? $ipLocation['longitude'],
            'city'      => $ipLocation['city'] ?? 'UNKNOWN',
            'province'  => $ipLocation['province'] ?? 'UNKNOWN',
            'country'   => $ipLocation['country'] ?? 'UNKNOWN',
        ];
    }

    /**
     * Check various security restrictions.
     */
    private function checkSecurityRestrictions(string $ip, string $rateLimitKey, string $email, ?User $user): void
    {
        // Check if IP is blocked
        if ($this->isIpBlocked($ip, $user)) {
            Log::warning('🔒 Blocked IP attempted login', [
                'ip' => $ip,
                'email' => $email,
                'user_id' => $user?->id,
            ]);

            throw ValidationException::withMessages([
                'email' => 'IP Anda telah diblokir sementara. Silakan coba lagi nanti atau hubungi administrator.',
            ]);
        }

        // Check rate limiting
        if ($this->isRateLimited($rateLimitKey)) {
            $availableIn = RateLimiter::availableIn($rateLimitKey);
            $attempts = RateLimiter::attempts($rateLimitKey);
            
            // Fire suspicious activity for high attempt rates
            if ($user && $attempts >= self::SUSPICIOUS_ATTEMPTS_THRESHOLD) {
                event(new SuspiciousActivity($user, $ip, 'high_login_attempt_rate', [
                    'attempts' => $attempts,
                    'available_in' => $availableIn,
                ]));
            }

            throw ValidationException::withMessages([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam " . ceil($availableIn / 60) . " menit.",
            ]);
        }
    }

    /**
     * Check if IP is blocked for the given user type.
     */
    private function isIpBlocked(string $ip, ?User $user): bool
    {
        // Superadmin is exempt from IP blocking
        if ($user && $user->hasRoleType('superadmin')) {
            return false;
        }

        return IpLock::active()
            ->stillLocked()
            ->forIp($ip)
            ->exists();
    }

    /**
     * Check if requests are rate limited.
     */
    private function isRateLimited(string $key): bool
    {
        return RateLimiter::tooManyAttempts($key, self::MAX_LOGIN_ATTEMPTS);
    }

    /**
     * Attempt user authentication with enhanced error handling.
     */
    private function attemptAuthentication(array $credentials, Request $request, string $rateLimitKey, ?User $user, array $location): User
    {
        $loginData = [
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ];

        if (!Auth::guard('web')->attempt($loginData, $credentials['remember'])) {
            $this->handleFailedAuthentication($credentials['email'], $request->ip(), $rateLimitKey, $user, $request, $location);
            
            throw ValidationException::withMessages([
                'email' => $user ? 'Password tidak valid.' : 'Email atau password tidak valid.',
            ]);
        }

        return Auth::user();
    }

    /**
     * Enhanced failed authentication handling.
     */
    private function handleFailedAuthentication(string $email, string $ip, string $rateLimitKey, ?User $user, Request $request, array $location): void
    {
        RateLimiter::hit($rateLimitKey, self::LOCKOUT_MINUTES * 60);
        $attempts = RateLimiter::attempts($rateLimitKey);

        // Block IP after max attempts for non-privileged users
        if ($user && $attempts >= self::MAX_LOGIN_ATTEMPTS && 
            !$user->hasRoleType(['superadmin', 'outlet'])) {
            
            $this->blockIpAddress($ip, $user, $email);
        }

        // Track repeated failures from same IP across different emails (potential attack)
        $this->trackSuspiciousIpActivity($ip, $email, $attempts);

        Log::warning('❌ Authentication failed', [
            'email' => $email,
            'ip' => $ip,
            'attempts' => $attempts,
            'user_exists' => $user !== null,
            'user_agent' => $request->userAgent(),
            'location' => $location,
        ]);
    }

    /**
     * Track suspicious IP activity across multiple accounts.
     */
    private function trackSuspiciousIpActivity(string $ip, string $email, int $attempts): void
    {
        $cacheKey = "suspicious_ip_activity:{$ip}";
        $suspiciousData = Cache::get($cacheKey, []);
        
        if (!isset($suspiciousData[$email])) {
            $suspiciousData[$email] = 0;
        }
        
        $suspiciousData[$email] = $attempts;
        
        // Count unique emails attempted from this IP
        $uniqueEmailsCount = count($suspiciousData);
        $totalAttempts = array_sum($suspiciousData);
        
        Cache::put($cacheKey, $suspiciousData, now()->addHours(2));
        
        // Fire suspicious activity if multiple accounts targeted
        if ($uniqueEmailsCount >= 3 || $totalAttempts >= 10) {
            event(new SuspiciousActivity(null, $ip, 'multiple_account_targeting', [
                'unique_emails' => $uniqueEmailsCount,
                'total_attempts' => $totalAttempts,
                'emails' => array_keys($suspiciousData),
            ]));
        }
    }

    /**
     * Block IP address after repeated failed attempts.
     */
    private function blockIpAddress(string $ip, User $user, string $email): void
    {
        // Check if IP is already blocked to avoid duplicates
        if ($this->isIpBlocked($ip, $user)) {
            return;
        }

        $location = $this->locationService->getLocation($ip);

        $ipLock = IpLock::create([
            'ip_address'   => $ip,
            'lock_type'    => 'temporary',
            'locked_at'    => now(),
            'unlocked_at'  => now()->addMinutes(self::LOCKOUT_MINUTES),
            'reason'       => "Exceeded maximum login attempts for email: {$email}",
            'locked_by'    => null,
            'city'         => $location['city'] ?? null,
            'province'     => $location['province'] ?? null,
            'success'      => false,
            'logged_in_at' => now(),
            'is_active'    => true,
        ]);

        // Fire IP blocked event
        event(new IpBlocked($ip, $user, $ipLock, [
            'trigger_email' => $email,
            'location' => $location,
        ]));

        Log::alert('🚫 IP automatically blocked', [
            'ip' => $ip,
            'user_id' => $user->id,
            'email' => $user->email,
            'trigger_email' => $email,
            'location' => $location,
            'blocked_until' => $ipLock->unlocked_at->toISOString(),
        ]);
    }

    /**
     * Validate geographic restrictions.
     */
    private function validateGeographicRestrictions(Request $request, string $ip, User $user, array $location): void
    {
        // Geographic restriction (except for privileged roles)
        if (!$user->hasRoleType(['superadmin', 'outlet']) && 
            !in_array($location['province'], self::ALLOWED_PROVINCES)) {
            
            Auth::logout();
            
            // Fire suspicious activity for geographic violation
            event(new SuspiciousActivity($user, $ip, 'geographic_restriction_violation', [
                'attempted_province' => $location['province'],
                'allowed_provinces' => self::ALLOWED_PROVINCES,
                'location' => $location,
            ]));
            
            Log::warning('⛔ Geographic restriction violated', [
                'user_id' => $user->id,
                'email' => $user->email,
                'province' => $location['province'],
                'ip' => $ip,
                'location' => $location,
            ]);

            throw ValidationException::withMessages([
                'email' => 'Login hanya diperbolehkan dari wilayah Kepulauan Riau.',
            ]);
        }
    }

    /**
     * Enhanced failed login event handling.
     */
    private function handleFailedLoginEvent(string $email, string $ip, Request $request, array $location, ?User $user, ValidationException $exception): void
    {
        $rateLimitKey = $this->getRateLimitKey($email, $ip);
        $attemptCount = RateLimiter::attempts($rateLimitKey);

        // Fire comprehensive failed login event
        event(new UserLoginFailed(
            email: $email,
            ipAddress: $ip,
            userAgent: $request->userAgent(),
            locationData: $location,
            attemptCount: $attemptCount,
            user: $user,
            failureReason: $exception->getMessage(),
            additionalContext: [
                'has_remember' => $request->boolean('remember'),
                'session_id' => session()->getId(),
                'timestamp' => now()->toISOString(),
            ]
        ));
    }

    /**
     * Manage active user sessions.
     */
    private function manageActiveSessions(User $user): void
    {
        // Auto-logout idle sessions
        $this->cleanupIdleSessions($user);

        // Limit concurrent sessions
        $this->limitConcurrentSessions($user);
    }

    /**
     * Cleanup idle sessions.
     */
    private function cleanupIdleSessions(User $user): void
    {
        $idleThreshold = now()->subMinutes(self::SESSION_TIMEOUT_MINUTES);
        
        $idleSessionCount = SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('last_activity_at', '<', $idleThreshold)
            ->update([
                'is_active' => false,
                'logged_out_at' => now(),
            ]);

        if ($idleSessionCount > 0) {
            Log::info('🕐 Idle sessions cleaned up', [
                'user_id' => $user->id,
                'cleaned_count' => $idleSessionCount,
            ]);
        }
    }

    /**
     * Limit concurrent active sessions.
     */
    private function limitConcurrentSessions(User $user): void
    {
        $activeSessions = SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->orderBy('last_activity_at', 'asc')
            ->get();

        if ($activeSessions->count() >= self::MAX_ACTIVE_SESSIONS) {
            $sessionsToTerminate = $activeSessions->take(
                $activeSessions->count() - self::MAX_ACTIVE_SESSIONS + 1
            );

            foreach ($sessionsToTerminate as $session) {
                $session->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                ]);
            }

            Log::info('🔄 Old sessions terminated due to limit', [
                'user_id' => $user->id,
                'terminated_count' => $sessionsToTerminate->count(),
                'remaining_active' => self::MAX_ACTIVE_SESSIONS - 1,
            ]);
        }
    }

    /**
     * Create new session login record.
     */
    private function createSessionRecord(User $user, Request $request, string $ip, array $location): void
    {
        SessionLogin::create([
            'user_id'          => $user->id,
            'session_id'       => session()->getId(),
            'ip_address'       => $ip,
            'user_agent'       => $request->userAgent(),
            'device'           => $this->detectDevice($request->userAgent()),
            'city'             => $location['city'],
            'province'         => $location['province'],
            'latitude'         => $location['latitude'],
            'longitude'        => $location['longitude'],
            'success'          => true,
            'is_active'        => true,
            'logged_in_at'     => now(),
            'last_activity_at' => now(),
        ]);

        // Update user's last login info
        $user->update([
            'last_login_at' => now(),
            'last_ip' => $ip,
            'last_location' => $location['city'] . ', ' . $location['province'],
        ]);
    }

    /**
     * Enhanced device detection.
     */
    private function detectDevice(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);
        
        if (preg_match('/mobile|android|iphone/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/tablet|ipad/i', $userAgent)) {
            return 'tablet';
        } elseif (preg_match('/bot|crawler|spider/i', $userAgent)) {
            return 'bot';
        }
        
        return 'desktop';
    }

    /**
     * Finalize successful login.
     */
    private function finalizeSuccessfulLogin(Request $request, string $rateLimitKey): void
    {
        $request->session()->regenerate();
        RateLimiter::clear($rateLimitKey);
        
        // Clear any cached failed attempts
        Cache::forget("failed_logins:" . $request->ip());
        Cache::forget("suspicious_ip_activity:" . $request->ip());
    }

    /**
     * Terminate a specific user session.
     */
    private function terminateUserSession(User $user, string $sessionId): void
    {
        SessionLogin::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->where('is_active', true)
            ->update([
                'is_active' => false,
                'logged_out_at' => now(),
            ]);
    }

    /**
     * Enhanced successful login logging.
     */
    private function logSuccessfulLogin(User $user, string $ip): void
    {
        Log::info('✅ User authenticated successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role_type,
            'ip' => $ip,
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Log user logout.
     */
    private function logUserLogout(User $user, string $ip): void
    {
        Log::info('👋 User logged out', [
            'user_id' => $user->id,
            'email' => $user->email,
            'ip' => $ip,
            'session_id' => session()->getId(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    /**
     * Get redirect URL based on user role with fallback.
     */
    private function getRedirectUrl(string $role): string
    {
        $routes = [
            'superadmin' => 'admins.dashboard',
            'admin'      => 'admin.dashboard',
            'outlet'     => 'outlet.dashboard',
            'doctor'     => 'doctor.dashboard',
            'companies'  => 'company.dashboard',
            'patient'    => 'patient.dashboard',
        ];

        $routeName = $routes[$role] ?? 'dashboard';

        // Check if route exists before redirecting
        try {
            return route($routeName);
        } catch (\Exception $e) {
            Log::warning("Route {$routeName} does not exist, using fallback", [
                'role' => $role,
                'error' => $e->getMessage(),
            ]);
            
            return route('dashboard');
        }
    }

    /**
     * Force logout all sessions for a user (admin function).
     */
    public function forceLogoutAllSessions(User $user, ?User $admin = null): bool
    {
        try {
            DB::beginTransaction();

            $sessionCount = SessionLogin::where('user_id', $user->id)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                ]);

            DB::commit();

            Log::info('🔒 All sessions force terminated', [
                'target_user_id' => $user->id,
                'terminated_sessions' => $sessionCount,
                'admin_user_id' => $admin?->id,
                'admin_action' => $admin !== null,
                'timestamp' => now()->toISOString(),
            ]);

            return true;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to force terminate sessions', [
                'target_user_id' => $user->id,
                'admin_user_id' => $admin?->id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    /**
     * Get login statistics for monitoring.
     */
    public function getLoginStatistics(int $days = 7): array
    {
        $startDate = now()->subDays($days);
        
        return [
            'successful_logins' => SessionLogin::where('success', true)
                ->where('logged_in_at', '>=', $startDate)
                ->count(),
            'failed_attempts' => SessionLogin::where('success', false)
                ->where('logged_in_at', '>=', $startDate)
                ->count(),
            'blocked_ips' => IpLock::where('created_at', '>=', $startDate)
                ->count(),
            'unique_users' => SessionLogin::where('success', true)
                ->where('logged_in_at', '>=', $startDate)
                ->distinct('user_id')
                ->count(),
        ];
    }
}