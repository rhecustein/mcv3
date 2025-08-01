<?php

namespace App\Listeners;

use App\Events\UserLoggedIn;
use App\Events\UserLoginFailed;
use App\Events\IpBlocked;
use App\Events\SuspiciousActivity;
use App\Models\SessionLogin;
use App\Models\IpLock;
use App\Models\Notification;
use App\Models\User;
use App\Services\SecurityAlertService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;

/**
 * Enhanced Listener untuk UserLoginFailed Event
 */
class HandleUserLoginFailed
{
    private SecurityAlertService $securityAlert;
    private NotificationService $notificationService;

    public function __construct(
        SecurityAlertService $securityAlert,
        NotificationService $notificationService
    ) {
        $this->securityAlert = $securityAlert;
        $this->notificationService = $notificationService;
    }

    public function handle(UserLoginFailed $event): void
    {
        // Log percobaan login gagal dengan detail lengkap
        $this->logFailedAttempt($event);

        // Catat ke database session_logins
        $this->recordFailedSession($event);

        // Handle IP blocking otomatis
        $this->handleAutomaticIpBlocking($event);

        // Monitor pola serangan
        $this->monitorAttackPatterns($event);

        // Send alerts untuk critical attempts
        if ($event->isCriticalAttempt()) {
            $this->handleCriticalAttempt($event);
        }

        // Track geolocation anomalies
        if ($event->user) {
            $this->trackGeolocationAnomalies($event);
        }
    }

    protected function logFailedAttempt(UserLoginFailed $event): void
    {
        Log::warning('🚫 Login attempt failed', [
            'email' => $event->email,
            'ip' => $event->ipAddress,
            'location' => $event->getLocationString(),
            'attempt_count' => $event->attemptCount,
            'user_agent' => substr($event->userAgent, 0, 200),
            'user_exists' => $event->user !== null,
            'user_id' => $event->getUserId(),
            'failure_reason' => $event->failureReason ?? 'Invalid credentials',
            'is_critical' => $event->isCriticalAttempt(),
            'is_high_risk' => $event->isHighRiskAttempt(),
            'from_unknown_location' => $event->isFromUnknownLocation(),
            'context' => $event->additionalContext,
            'timestamp' => now()->toISOString(),
        ]);
    }

    protected function recordFailedSession(UserLoginFailed $event): void
    {
        SessionLogin::create([
            'user_id' => $event->getUserId(),
            'session_id' => null,
            'ip_address' => $event->ipAddress,
            'user_agent' => $event->userAgent,
            'device' => $this->detectDevice($event->userAgent),
            'city' => $event->locationData['city'] ?? null,
            'province' => $event->locationData['province'] ?? null,
            'latitude' => $event->locationData['latitude'] ?? null,
            'longitude' => $event->locationData['longitude'] ?? null,
            'success' => false,
            'is_active' => false,
            'logged_in_at' => now(),
            'last_activity_at' => now(),
        ]);
    }

    protected function handleAutomaticIpBlocking(UserLoginFailed $event): void
    {
        // Skip blocking untuk superadmin dan outlet
        if ($event->user && $event->user->hasRoleType(['superadmin', 'outlet'])) {
            return;
        }

        if ($event->isCriticalAttempt()) {
            $existingLock = IpLock::where('ip_address', $event->ipAddress)
                ->where('is_active', true)
                ->first();

            if (!$existingLock) {
                $ipLock = IpLock::create([
                    'ip_address' => $event->ipAddress,
                    'lock_type' => 'temporary',
                    'reason' => "Automated block: {$event->attemptCount}x failed login attempts for {$event->email}",
                    'locked_at' => now(),
                    'unlocked_at' => now()->addMinutes(30),
                    'locked_by' => null,
                    'city' => $event->locationData['city'] ?? null,
                    'province' => $event->locationData['province'] ?? null,
                    'success' => false,
                    'logged_in_at' => now(),
                    'is_active' => true,
                ]);

                // Fire IP blocked event
                event(new IpBlocked($event->ipAddress, $event->user, $ipLock, [
                    'trigger' => 'automatic_login_failure_limit',
                    'failed_email' => $event->email,
                    'attempt_count' => $event->attemptCount,
                ]));

                Log::alert('🔒 IP automatically blocked', [
                    'ip' => $event->ipAddress,
                    'email' => $event->email,
                    'user_id' => $event->getUserId(),
                    'location' => $event->getLocationString(),
                    'attempt_count' => $event->attemptCount,
                    'blocked_until' => $ipLock->unlocked_at->toISOString(),
                ]);
            }
        }
    }

    protected function monitorAttackPatterns(UserLoginFailed $event): void
    {
        $cacheKey = "attack_pattern:{$event->ipAddress}";
        $attackData = Cache::get($cacheKey, [
            'emails' => [],
            'total_attempts' => 0,
            'first_attempt' => now(),
            'last_attempt' => now(),
        ]);

        if (!in_array($event->email, $attackData['emails'])) {
            $attackData['emails'][] = $event->email;
        }

        $attackData['total_attempts'] = $event->attemptCount;
        $attackData['last_attempt'] = now();

        Cache::put($cacheKey, $attackData, now()->addHours(2));

        // Detect brute force patterns
        if (count($attackData['emails']) >= 3) {
            event(new SuspiciousActivity(
                $event->user,
                $event->ipAddress,
                'brute_force_multiple_accounts',
                [
                    'targeted_emails' => $attackData['emails'],
                    'total_attempts' => $attackData['total_attempts'],
                    'duration_minutes' => $attackData['first_attempt']->diffInMinutes($attackData['last_attempt']),
                ],
                'high'
            ));
        }
    }

    protected function handleCriticalAttempt(UserLoginFailed $event): void
    {
        // Send notification to user if account exists
        if ($event->user) {
            $this->notificationService->sendSecurityAlert($event->user, [
                'type' => 'failed_login_attempts',
                'ip' => $event->ipAddress,
                'location' => $event->getLocationString(),
                'attempt_count' => $event->attemptCount,
                'timestamp' => now(),
            ]);
        }

        // Send alert to administrators
        $this->securityAlert->sendAdminAlert('critical_login_failures', [
            'email' => $event->email,
            'ip' => $event->ipAddress,
            'location' => $event->getLocationString(),
            'attempt_count' => $event->attemptCount,
            'user_exists' => $event->user !== null,
        ]);

        // Store critical attempt for monitoring dashboard
        Cache::put("critical_attempt:{$event->ipAddress}:{$event->email}", [
            'email' => $event->email,
            'ip' => $event->ipAddress,
            'location' => $event->getLocationString(),
            'attempts' => $event->attemptCount,
            'timestamp' => now()->toISOString(),
            'user_id' => $event->getUserId(),
        ], now()->addHours(24));
    }

    protected function trackGeolocationAnomalies(UserLoginFailed $event): void
    {
        if (!$event->user) return;

        $lastSuccessfulLogin = SessionLogin::where('user_id', $event->user->id)
            ->where('success', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->latest('logged_in_at')
            ->first();

        if ($lastSuccessfulLogin && 
            isset($event->locationData['latitude']) && 
            isset($event->locationData['longitude'])) {
            
            $distance = $this->calculateDistance(
                $lastSuccessfulLogin->latitude,
                $lastSuccessfulLogin->longitude,
                $event->locationData['latitude'],
                $event->locationData['longitude']
            );

            // If distance > 1000km, flag as suspicious
            if ($distance > 1000) {
                event(new SuspiciousActivity(
                    $event->user,
                    $event->ipAddress,
                    'geolocation_anomaly',
                    [
                        'current_location' => $event->getLocationString(),
                        'last_successful_location' => "{$lastSuccessfulLogin->city}, {$lastSuccessfulLogin->province}",
                        'distance_km' => round($distance, 2),
                        'time_since_last_login' => $lastSuccessfulLogin->logged_in_at->diffForHumans(),
                    ],
                    'medium'
                ));
            }
        }
    }

    protected function detectDevice(string $userAgent): string
    {
        $userAgent = strtolower($userAgent);

        if (str_contains($userAgent, 'mobile') || str_contains($userAgent, 'android')) {
            return 'mobile';
        }

        if (str_contains($userAgent, 'tablet') || str_contains($userAgent, 'ipad')) {
            return 'tablet';
        }

        if (str_contains($userAgent, 'bot') || str_contains($userAgent, 'crawler')) {
            return 'bot';
        }

        return 'desktop';
    }

    protected function calculateDistance(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1-$a));

        return $earthRadius * $c;
    }
}

/**
 * Listener untuk UserLoggedIn Event
 */
class HandleUserLoggedIn
{
    public function handle(UserLoggedIn $event): void
    {
        // Log successful login
        $this->logSuccessfulLogin($event);

        // Update user login statistics
        $this->updateUserStatistics($event);

        // Check for unusual login patterns
        $this->checkUnusualPatterns($event);

        // Clear failed attempt caches
        $this->clearFailedAttemptCaches($event);
    }

    protected function logSuccessfulLogin(UserLoggedIn $event): void
    {
        Log::info('✅ User successfully logged in', [
            'user_id' => $event->user->id,
            'email' => $event->user->email,
            'role' => $event->user->role_type,
            'ip' => $event->ipAddress,
            'location' => $event->getLocationString(),
            'session_id' => $event->sessionId,
            'user_agent' => substr($event->userAgent, 0, 200),
            'timestamp' => now()->toISOString(),
        ]);
    }

    protected function updateUserStatistics(UserLoggedIn $event): void
    {
        $cacheKey = "user_login_stats:{$event->user->id}";
        $stats = Cache::get($cacheKey, [
            'total_logins' => 0,
            'last_login' => null,
            'login_ips' => [],
            'login_locations' => [],
        ]);

        $stats['total_logins']++;
        $stats['last_login'] = now()->toISOString();
        
        if (!in_array($event->ipAddress, $stats['login_ips'])) {
            $stats['login_ips'][] = $event->ipAddress;
            // Keep only last 10 IPs
            $stats['login_ips'] = array_slice($stats['login_ips'], -10);
        }

        $location = $event->getLocationString();
        if (!in_array($location, $stats['login_locations'])) {
            $stats['login_locations'][] = $location;
            // Keep only last 10 locations
            $stats['login_locations'] = array_slice($stats['login_locations'], -10);
        }

        Cache::put($cacheKey, $stats, now()->addDays(30));
    }

    protected function checkUnusualPatterns(UserLoggedIn $event): void
    {
        // Check for rapid successive logins from different IPs
        $recentLogins = SessionLogin::where('user_id', $event->user->id)
            ->where('success', true)
            ->where('logged_in_at', '>=', now()->subMinutes(10))
            ->get();

        if ($recentLogins->count() > 3) {
            $uniqueIps = $recentLogins->pluck('ip_address')->unique();
            
            if ($uniqueIps->count() > 2) {
                event(new SuspiciousActivity(
                    $event->user,
                    $event->ipAddress,
                    'rapid_multi_ip_logins',
                    [
                        'login_count' => $recentLogins->count(),
                        'unique_ips' => $uniqueIps->toArray(),
                        'time_window_minutes' => 10,
                    ],
                    'medium'
                ));
            }
        }
    }

    protected function clearFailedAttemptCaches(UserLoggedIn $event): void
    {
        Cache::forget("failed_logins:{$event->ipAddress}");
        Cache::forget("attack_pattern:{$event->ipAddress}");
        Cache::forget("suspicious_ip_activity:{$event->ipAddress}");
    }
}

/**
 * Listener untuk IpBlocked Event
 */
class HandleIpBlocked
{
    private SecurityAlertService $securityAlert;

    public function __construct(SecurityAlertService $securityAlert)
    {
        $this->securityAlert = $securityAlert;
    }

    public function handle(IpBlocked $event): void
    {
        // Log IP blocking
        $this->logIpBlocked($event);

        // Send notifications
        $this->sendBlockingNotifications($event);

        // Update security metrics
        $this->updateSecurityMetrics($event);
    }

    protected function logIpBlocked(IpBlocked $event): void
    {
        Log::critical('🚫 IP address blocked', [
            'ip' => $event->ipAddress,
            'user_id' => $event->user?->id,
            'user_email' => $event->user?->email,
            'lock_type' => $event->ipLock->lock_type,
            'reason' => $event->ipLock->reason,
            'blocked_until' => $event->ipLock->unlocked_at?->toISOString(),
            'duration_minutes' => $event->getBlockDuration(),
            'additional_data' => $event->additionalData,
            'timestamp' => now()->toISOString(),
        ]);
    }

    protected function sendBlockingNotifications(IpBlocked $event): void
    {
        // Notify affected user if exists
        if ($event->user) {
            Notification::create([
                'user_id' => $event->user->id,
                'title' => 'Keamanan Akun: IP Diblokir',
                'message' => "IP address {$event->ipAddress} telah diblokir karena percobaan login yang mencurigakan.",
                'type' => 'security_alert',
                'via_in_app' => true,
                'via_email' => true,
            ]);
        }

        // Send alert to administrators
        $this->securityAlert->sendAdminAlert('ip_blocked', [
            'ip' => $event->ipAddress,
            'user' => $event->user?->email,
            'reason' => $event->ipLock->reason,
            'duration' => $event->getBlockDuration(),
        ]);
    }

    protected function updateSecurityMetrics(IpBlocked $event): void
    {
        $today = now()->format('Y-m-d');
        $metricsKey = "security_metrics:{$today}";
        
        $metrics = Cache::get($metricsKey, [
            'blocked_ips' => 0,
            'temporary_blocks' => 0,
            'permanent_blocks' => 0,
        ]);

        $metrics['blocked_ips']++;
        
        if ($event->isTemporary()) {
            $metrics['temporary_blocks']++;
        } else {
            $metrics['permanent_blocks']++;
        }

        Cache::put($metricsKey, $metrics, now()->addDays(7));
    }
}

/**
 * Listener untuk SuspiciousActivity Event
 */
class HandleSuspiciousActivity
{
    private SecurityAlertService $securityAlert;

    public function __construct(SecurityAlertService $securityAlert)
    {
        $this->securityAlert = $securityAlert;
    }

    public function handle(SuspiciousActivity $event): void
    {
        // Log suspicious activity
        $this->logSuspiciousActivity($event);

        // Handle critical activities immediately
        if ($event->isCritical()) {
            $this->handleCriticalActivity($event);
        }

        // Store for analysis
        $this->storeForAnalysis($event);

        // Update threat intelligence
        $this->updateThreatIntelligence($event);
    }

    protected function logSuspiciousActivity(SuspiciousActivity $event): void
    {
        Log::warning('⚠️ Suspicious activity detected', [
            'user_id' => $event->user?->id,
            'user_email' => $event->user?->email,
            'ip' => $event->ipAddress,
            'activity_type' => $event->activityType,
            'description' => $event->getActivityDescription(),
            'severity' => $event->severity,
            'context' => $event->context,
            'is_critical' => $event->isCritical(),
            'timestamp' => now()->toISOString(),
        ]);
    }

    protected function handleCriticalActivity(SuspiciousActivity $event): void
    {
        // Immediate admin notification for critical activities
        $this->securityAlert->sendCriticalAlert([
            'type' => 'suspicious_activity',
            'activity' => $event->activityType,
            'description' => $event->getActivityDescription(),
            'user' => $event->user?->email,
            'ip' => $event->ipAddress,
            'context' => $event->context,
        ]);

        // Consider automatic actions for critical activities
        if ($event->activityType === 'multiple_account_targeting' && !$event->user?->hasRoleType('superadmin')) {
            // Auto-block IP for critical threats
            IpLock::create([
                'ip_address' => $event->ipAddress,
                'lock_type' => 'temporary',
                'reason' => "Critical suspicious activity: {$event->activityType}",
                'locked_at' => now(),
                'unlocked_at' => now()->addHours(2),
                'locked_by' => null,
                'is_active' => true,
            ]);
        }
    }

    protected function storeForAnalysis(SuspiciousActivity $event): void
    {
        $analysisKey = "suspicious_activities:" . now()->format('Y-m-d');
        $activities = Cache::get($analysisKey, []);
        
        $activities[] = [
            'timestamp' => now()->toISOString(),
            'user_id' => $event->user?->id,
            'ip' => $event->ipAddress,
            'activity_type' => $event->activityType,
            'severity' => $event->severity,
            'context' => $event->context,
        ];

        Cache::put($analysisKey, $activities, now()->addDays(7));
    }

    protected function updateThreatIntelligence(SuspiciousActivity $event): void
    {
        $threatKey = "threat_intel:{$event->ipAddress}";
        $threatData = Cache::get($threatKey, [
            'first_seen' => now()->toISOString(),
            'last_seen' => now()->toISOString(),
            'activity_count' => 0,
            'activity_types' => [],
            'severity_levels' => [],
        ]);

        $threatData['last_seen'] = now()->toISOString();
        $threatData['activity_count']++;
        
        if (!in_array($event->activityType, $threatData['activity_types'])) {
            $threatData['activity_types'][] = $event->activityType;
        }
        
        if (!in_array($event->severity, $threatData['severity_levels'])) {
            $threatData['severity_levels'][] = $event->severity;
        }

        Cache::put($threatKey, $threatData, now()->addDays(30));
    }
}