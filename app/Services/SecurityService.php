<?php

namespace App\Services;

use App\Models\IpLock;
use App\Models\SessionLogin;
use App\Models\User;
use App\Events\SuspiciousActivityDetected;
use App\Events\IpBlocked;
use App\Events\SecurityBreach;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SecurityService
{
    // Security Constants
    private const MAX_LOGIN_ATTEMPTS = 5;
    private const LOCKOUT_MINUTES = 30;
    private const MAX_ACTIVE_SESSIONS = 3;
    private const SESSION_TIMEOUT_MINUTES = 30;
    private const SUSPICIOUS_ACTIVITY_THRESHOLD = 10;
    private const RATE_LIMIT_WINDOW = 60; // seconds
    
    // Geographic restrictions
    private const ALLOWED_PROVINCES = ['Kepulauan Riau'];
    private const EXEMPT_ROLES = ['superadmin', 'outlet'];

    /**
     * Check if an IP address is blocked for a specific user.
     */
    public function isIpBlocked(string $ip, ?User $user = null): bool
    {
        // Superadmin and outlet roles are exempt from IP blocking
        if ($user && $user->hasRoleType(self::EXEMPT_ROLES)) {
            return false;
        }

        $cacheKey = "ip_blocked:{$ip}";
        
        return Cache::remember($cacheKey, 300, function () use ($ip) {
            return IpLock::active()
                ->stillLocked()
                ->forIp($ip)
                ->exists();
        });
    }

    /**
     * Block an IP address with detailed logging and event firing.
     */
    public function blockIp(
        string $ip, 
        string $reason, 
        ?User $blockedBy = null, 
        int $minutes = self::LOCKOUT_MINUTES,
        string $lockType = 'temporary'
    ): IpLock {
        try {
            DB::beginTransaction();

            $ipLock = IpLock::create([
                'ip_address'   => $ip,
                'lock_type'    => $lockType,
                'locked_at'    => now(),
                'unlocked_at'  => $lockType === 'temporary' ? now()->addMinutes($minutes) : null,
                'reason'       => $reason,
                'locked_by'    => $blockedBy?->id,
                'is_active'    => true,
            ]);

            // Clear cache
            Cache::forget("ip_blocked:{$ip}");

            // Log the security event
            Log::alert('🚫 IP Address Blocked', [
                'ip' => $ip,
                'reason' => $reason,
                'lock_type' => $lockType,
                'duration_minutes' => $minutes,
                'blocked_by' => $blockedBy?->id,
                'blocked_by_email' => $blockedBy?->email,
                'expires_at' => $ipLock->unlocked_at?->toISOString(),
            ]);

            // Fire security event
            event(new IpBlocked($ip, $blockedBy, $ipLock));

            DB::commit();
            return $ipLock;

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to block IP address', [
                'ip' => $ip,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw $e;
        }
    }

    /**
     * Unblock an IP address.
     */
    public function unblockIp(string $ip, ?User $unblockedBy = null): bool
    {
        try {
            $updatedCount = IpLock::where('ip_address', $ip)
                ->where('is_active', true)
                ->update([
                    'is_active' => false,
                    'unlocked_at' => now(),
                    'updated_at' => now(),
                ]);

            if ($updatedCount > 0) {
                // Clear cache
                Cache::forget("ip_blocked:{$ip}");

                Log::info('✅ IP Address Unblocked', [
                    'ip' => $ip,
                    'unblocked_by' => $unblockedBy?->id,
                    'unblocked_by_email' => $unblockedBy?->email,
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('Failed to unblock IP address', [
                'ip' => $ip,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /**
     * Check if request is rate limited.
     */
    public function isRateLimited(string $key, int $maxAttempts = self::MAX_LOGIN_ATTEMPTS): bool
    {
        return RateLimiter::tooManyAttempts($key, $maxAttempts);
    }

    /**
     * Get rate limit attempts count and available time.
     */
    public function getRateLimitInfo(string $key): array
    {
        return [
            'attempts' => RateLimiter::attempts($key),
            'available_in' => RateLimiter::availableIn($key),
            'max_attempts' => self::MAX_LOGIN_ATTEMPTS,
        ];
    }

    /**
     * Validate geographic restrictions.
     */
    public function validateGeographicRestriction(array $location, User $user): bool
    {
        // Exempt privileged roles from geographic restrictions
        if ($user->hasRoleType(self::EXEMPT_ROLES)) {
            return true;
        }

        $province = $location['province'] ?? 'UNKNOWN';
        $isAllowed = in_array($province, self::ALLOWED_PROVINCES);

        if (!$isAllowed) {
            Log::warning('⛔ Geographic restriction violation', [
                'user_id' => $user->id,
                'email' => $user->email,
                'province' => $province,
                'allowed_provinces' => self::ALLOWED_PROVINCES,
            ]);

            // Fire suspicious activity event
            event(new SuspiciousActivityDetected($user, 'geographic_violation', [
                'province' => $province,
                'location' => $location,
            ]));
        }

        return $isAllowed;
    }

    /**
     * Detect and handle suspicious login patterns.
     */
    public function detectSuspiciousActivity(User $user, array $loginData): bool
    {
        $suspiciousFactors = [];
        $riskScore = 0;

        // Check for multiple failed attempts
        $failedAttempts = $this->getRecentFailedAttempts($user->email);
        if ($failedAttempts >= 3) {
            $suspiciousFactors[] = 'multiple_failed_attempts';
            $riskScore += 30;
        }

        // Check for login from new location
        if ($this->isNewLocation($user, $loginData)) {
            $suspiciousFactors[] = 'new_location';
            $riskScore += 20;
        }

        // Check for unusual time pattern
        if ($this->isUnusualTime($user)) {
            $suspiciousFactors[] = 'unusual_time';
            $riskScore += 15;
        }

        // Check for multiple IPs in short time
        if ($this->hasMultipleRecentIPs($user)) {
            $suspiciousFactors[] = 'multiple_ips';
            $riskScore += 25;
        }

        // Check for concurrent sessions from different locations
        if ($this->hasConcurrentSessionsFromDifferentLocations($user)) {
            $suspiciousFactors[] = 'concurrent_different_locations';
            $riskScore += 40;
        }

        $isSuspicious = $riskScore >= 50;

        if ($isSuspicious) {
            Log::warning('🔍 Suspicious activity detected', [
                'user_id' => $user->id,
                'email' => $user->email,
                'risk_score' => $riskScore,
                'factors' => $suspiciousFactors,
                'login_data' => $loginData,
            ]);

            // Fire suspicious activity event
            event(new SuspiciousActivityDetected($user, 'login_pattern', [
                'risk_score' => $riskScore,
                'factors' => $suspiciousFactors,
                'login_data' => $loginData,
            ]));

            // If risk score is very high, consider blocking
            if ($riskScore >= 80) {
                $this->blockIp(
                    $loginData['ip'],
                    'High-risk suspicious activity detected (score: ' . $riskScore . ')',
                    null,
                    60, // 1 hour block
                    'temporary'
                );
            }
        }

        return $isSuspicious;
    }

    /**
     * Get recent failed login attempts for an email.
     */
    private function getRecentFailedAttempts(string $email): int
    {
        $cacheKey = "failed_attempts:{$email}";
        
        return Cache::remember($cacheKey, 300, function () use ($email) {
            return SessionLogin::where('user_id', function ($query) use ($email) {
                    $query->select('id')
                        ->from('users')
                        ->where('email', $email)
                        ->limit(1);
                })
                ->where('success', false)
                ->where('logged_in_at', '>=', now()->subHours(2))
                ->count();
        });
    }

    /**
     * Check if login is from a new location.
     */
    private function isNewLocation(User $user, array $loginData): bool
    {
        $recentLocations = SessionLogin::where('user_id', $user->id)
            ->where('success', true)
            ->where('logged_in_at', '>=', now()->subDays(7))
            ->distinct()
            ->pluck('city')
            ->toArray();

        return !in_array($loginData['city'] ?? 'UNKNOWN', $recentLocations);
    }

    /**
     * Check if login time is unusual for the user.
     */
    private function isUnusualTime(User $user): bool
    {
        $currentHour = now()->hour;
        
        // Get user's typical login hours
        $typicalHours = SessionLogin::where('user_id', $user->id)
            ->where('success', true)
            ->where('logged_in_at', '>=', now()->subDays(30))
            ->selectRaw('HOUR(logged_in_at) as hour, COUNT(*) as count')
            ->groupBy('hour')
            ->pluck('count', 'hour')
            ->toArray();

        if (empty($typicalHours)) {
            return false; // New user, can't determine pattern
        }

        $currentHourLogins = $typicalHours[$currentHour] ?? 0;
        $averageLogins = array_sum($typicalHours) / count($typicalHours);

        // Consider unusual if less than 20% of average activity
        return $currentHourLogins < ($averageLogins * 0.2);
    }

    /**
     * Check if user has logged in from multiple IPs recently.
     */
    private function hasMultipleRecentIPs(User $user): bool
    {
        $recentIPs = SessionLogin::where('user_id', $user->id)
            ->where('logged_in_at', '>=', now()->subHours(2))
            ->distinct()
            ->count('ip_address');

        return $recentIPs >= 3;
    }

    /**
     * Check for concurrent sessions from different locations.
     */
    private function hasConcurrentSessionsFromDifferentLocations(User $user): bool
    {
        $activeSessions = SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->select('city', 'province')
            ->distinct()
            ->count();

        return $activeSessions >= 2;
    }

    /**
     * Manage user session limits.
     */
    public function enforceSessionLimits(User $user): array
    {
        $terminatedSessions = [];

        try {
            // Auto-logout idle sessions
            $idleSessions = SessionLogin::where('user_id', $user->id)
                ->where('is_active', true)
                ->where('last_activity_at', '<', now()->subMinutes(self::SESSION_TIMEOUT_MINUTES))
                ->get();

            foreach ($idleSessions as $session) {
                $session->update([
                    'is_active' => false,
                    'logged_out_at' => now(),
                ]);
                $terminatedSessions[] = ['reason' => 'idle', 'session_id' => $session->session_id];
            }

            // Limit concurrent sessions
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
                    $terminatedSessions[] = ['reason' => 'limit', 'session_id' => $session->session_id];
                }
            }

            if (!empty($terminatedSessions)) {
                Log::info('🔄 Sessions terminated', [
                    'user_id' => $user->id,
                    'terminated_sessions' => $terminatedSessions,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Failed to enforce session limits', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);
        }

        return $terminatedSessions;
    }

    /**
     * Generate security report for a user.
     */
    public function generateSecurityReport(User $user): array
    {
        $report = [
            'user_id' => $user->id,
            'email' => $user->email,
            'generated_at' => now()->toISOString(),
        ];

        // Active sessions
        $report['active_sessions'] = SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();

        // Recent login attempts
        $report['recent_logins'] = SessionLogin::where('user_id', $user->id)
            ->where('logged_in_at', '>=', now()->subDays(7))
            ->count();

        // Failed attempts
        $report['failed_attempts'] = SessionLogin::where('user_id', $user->id)
            ->where('success', false)
            ->where('logged_in_at', '>=', now()->subDays(7))
            ->count();

        // Unique locations
        $report['unique_locations'] = SessionLogin::where('user_id', $user->id)
            ->where('logged_in_at', '>=', now()->subDays(30))
            ->distinct()
            ->count('city');

        // Risk assessment
        $report['risk_level'] = $this->calculateUserRiskLevel($user);

        return $report;
    }

    /**
     * Calculate user risk level based on various factors.
     */
    private function calculateUserRiskLevel(User $user): string
    {
        $riskScore = 0;

        // Failed attempts factor
        $failedAttempts = $this->getRecentFailedAttempts($user->email);
        $riskScore += $failedAttempts * 10;

        // Multiple locations factor
        $uniqueLocations = SessionLogin::where('user_id', $user->id)
            ->where('logged_in_at', '>=', now()->subDays(7))
            ->distinct()
            ->count('city');
        
        if ($uniqueLocations > 3) {
            $riskScore += 20;
        }

        // Concurrent sessions factor
        $activeSessions = SessionLogin::where('user_id', $user->id)
            ->where('is_active', true)
            ->count();
        
        if ($activeSessions >= self::MAX_ACTIVE_SESSIONS) {
            $riskScore += 15;
        }

        // Determine risk level
        if ($riskScore >= 50) {
            return 'high';
        } elseif ($riskScore >= 25) {
            return 'medium';
        } else {
            return 'low';
        }
    }

    /**
     * Clean up expired security records.
     */
    public function cleanupExpiredRecords(): array
    {
        $cleaned = [
            'ip_locks' => 0,
            'session_logins' => 0,
        ];

        try {
            // Clean expired IP locks
            $cleaned['ip_locks'] = IpLock::where('is_active', true)
                ->where('lock_type', 'temporary')
                ->where('unlocked_at', '<', now())
                ->update([
                    'is_active' => false,
                    'updated_at' => now(),
                ]);

            // Clean old session login records (older than 90 days)
            $cleaned['session_logins'] = SessionLogin::where('logged_in_at', '<', now()->subDays(90))
                ->delete();

            // Clear related caches
            Cache::tags(['security', 'ip_blocks'])->flush();

            Log::info('🧹 Security cleanup completed', $cleaned);

        } catch (\Exception $e) {
            Log::error('Security cleanup failed', [
                'error' => $e->getMessage(),
            ]);
        }

        return $cleaned;
    }

    /**
     * Get security statistics for monitoring.
     */
    public function getSecurityStats(): array
    {
        return [
            'active_ip_blocks' => IpLock::active()->count(),
            'temporary_blocks' => IpLock::active()->where('lock_type', 'temporary')->count(),
            'permanent_blocks' => IpLock::active()->where('lock_type', 'permanent')->count(),
            'active_sessions' => SessionLogin::where('is_active', true)->count(),
            'failed_logins_today' => SessionLogin::where('success', false)
                ->whereDate('logged_in_at', today())
                ->count(),
            'successful_logins_today' => SessionLogin::where('success', true)
                ->whereDate('logged_in_at', today())
                ->count(),
        ];
    }
}