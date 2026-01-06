<?php

namespace App\Services;

use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;

/**
 * Service untuk menangani alert keamanan
 */
class SecurityAlertService
{
    private const ADMIN_EMAILS = [
        'admin@suratsehat.com',
        'security@suratsehat.com',
    ];

    /**
     * Send critical security alert to administrators
     */
    public function sendCriticalAlert(array $data): void
    {
        try {
            // Log critical alert
            Log::critical('🚨 CRITICAL SECURITY ALERT', $data);

            // Send email to administrators
            foreach (self::ADMIN_EMAILS as $email) {
                Mail::raw(
                    $this->formatCriticalAlertEmail($data),
                    function ($message) use ($email) {
                        $message->to($email)
                                ->subject('[CRITICAL] Security Alert - ' . config('app.name'));
                    }
                );
            }

            // Create system notification
            $this->createSystemNotification('critical_security_alert', $data);

            // Update alert metrics
            $this->updateAlertMetrics('critical');

        } catch (\Exception $e) {
            Log::error('Failed to send critical security alert', [
                'error' => $e->getMessage(),
                'alert_data' => $data,
            ]);
        }
    }

    /**
     * Send admin alert for various security events
     */
    public function sendAdminAlert(string $type, array $data): void
    {
        try {
            Log::alert("🔔 Admin Security Alert: {$type}", $data);

            // Create notification for admin users
            $adminUsers = User::where('role_type', 'superadmin')->get();
            
            foreach ($adminUsers as $admin) {
                Notification::create([
                    'user_id' => $admin->id,
                    'title' => $this->getAlertTitle($type),
                    'message' => $this->formatAlertMessage($type, $data),
                    'type' => 'security_alert',
                    'via_in_app' => true,
                    'via_email' => $this->shouldSendEmail($type),
                ]);
            }

            // Update alert metrics
            $this->updateAlertMetrics($type);

        } catch (\Exception $e) {
            Log::error('Failed to send admin security alert', [
                'type' => $type,
                'error' => $e->getMessage(),
                'alert_data' => $data,
            ]);
        }
    }

    /**
     * Format critical alert email content
     */
    private function formatCriticalAlertEmail(array $data): string
    {
        $content = "CRITICAL SECURITY ALERT\n";
        $content .= "========================\n\n";
        $content .= "Time: " . now()->toDateTimeString() . "\n";
        $content .= "Application: " . config('app.name') . "\n";
        $content .= "Environment: " . config('app.env') . "\n\n";

        foreach ($data as $key => $value) {
            $content .= ucfirst(str_replace('_', ' ', $key)) . ": ";
            $content .= is_array($value) ? json_encode($value) : $value;
            $content .= "\n";
        }

        $content .= "\nPlease investigate immediately.";
        
        return $content;
    }

    /**
     * Get alert title based on type
     */
    private function getAlertTitle(string $type): string
    {
        return match($type) {
            'critical_login_failures' => 'Critical Login Failures Detected',
            'ip_blocked' => 'IP Address Blocked',
            'suspicious_activity' => 'Suspicious Activity Detected',
            'brute_force_attack' => 'Brute Force Attack Detected',
            'geographic_anomaly' => 'Geographic Login Anomaly',
            default => 'Security Alert',
        };
    }

    /**
     * Format alert message
     */
    private function formatAlertMessage(string $type, array $data): string
    {
        return match($type) {
            'critical_login_failures' => "Multiple failed login attempts detected for {$data['email']} from IP {$data['ip']} ({$data['location']}). Total attempts: {$data['attempt_count']}",
            'ip_blocked' => "IP {$data['ip']} has been blocked. Reason: {$data['reason']}. Duration: {$data['duration']} minutes.",
            'suspicious_activity' => sprintf(
                "Suspicious activity detected from IP %s. Activity: %s",
                $data['ip'],
                $data['activity'] ?? 'Unknown'
            ),
            default => 'Security event detected. Please check logs for details.',
        };
    }

    /**
     * Determine if email should be sent for alert type
     */
    private function shouldSendEmail(string $type): bool
    {
        return in_array($type, [
            'critical_login_failures',
            'ip_blocked',
            'brute_force_attack',
        ]);
    }

    /**
     * Create system-wide notification
     */
    private function createSystemNotification(string $type, array $data): void
    {
        Notification::create([
            'user_id' => null, // System notification
            'title' => 'Critical Security Alert',
            'message' => $this->formatAlertMessage($type, $data),
            'type' => 'system_security_alert',
            'via_in_app' => true,
            'via_email' => false,
        ]);
    }

    /**
     * Update alert metrics for monitoring
     */
    private function updateAlertMetrics(string $type): void
    {
        $today = now()->format('Y-m-d');
        $metricsKey = "security_alert_metrics:{$today}";
        
        $metrics = Cache::get($metricsKey, []);
        $metrics[$type] = ($metrics[$type] ?? 0) + 1;
        $metrics['total'] = ($metrics['total'] ?? 0) + 1;

        Cache::put($metricsKey, $metrics, now()->addDays(7));
    }

    /**
     * Get daily security metrics
     */
    public function getDailyMetrics(string $date = null): array
    {
        $date = $date ?: now()->format('Y-m-d');
        $metricsKey = "security_alert_metrics:{$date}";
        
        return Cache::get($metricsKey, [
            'total' => 0,
            'critical_login_failures' => 0,
            'ip_blocked' => 0,
            'suspicious_activity' => 0,
        ]);
    }
}

/**
 * Service untuk menangani notifikasi keamanan ke user
 */
class NotificationService
{
    /**
     * Send security alert to specific user
     */
    public function sendSecurityAlert(User $user, array $alertData): void
    {
        try {
            $notification = Notification::create([
                'user_id' => $user->id,
                'title' => $this->getSecurityAlertTitle($alertData['type']),
                'message' => $this->formatSecurityAlertMessage($alertData),
                'type' => 'security_alert',
                'via_in_app' => true,
                'via_email' => $this->shouldSendSecurityEmail($alertData['type']),
                'action_url' => route('settings.session'),
                'action_text' => 'Lihat Aktivitas Login',
            ]);

            Log::info('Security alert sent to user', [
                'user_id' => $user->id,
                'alert_type' => $alertData['type'],
                'notification_id' => $notification->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send security alert to user', [
                'user_id' => $user->id,
                'alert_data' => $alertData,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send login location alert
     */
    public function sendLoginLocationAlert(User $user, array $locationData): void
    {
        $this->sendSecurityAlert($user, [
            'type' => 'new_location_login',
            'ip' => $locationData['ip'],
            'location' => $locationData['location'],
            'timestamp' => $locationData['timestamp'],
        ]);
    }

    /**
     * Send account compromise warning
     */
    public function sendCompromiseWarning(User $user, array $compromiseData): void
    {
        Notification::create([
            'user_id' => $user->id,
            'title' => '🚨 Peringatan Keamanan Akun',
            'message' => 'Terdeteksi aktivitas mencurigakan pada akun Anda. Silakan ganti password dan periksa aktivitas login.',
            'type' => 'security_warning',
            'via_in_app' => true,
            'via_email' => true,
            'action_url' => route('settings.profile.edit'),
            'action_text' => 'Ganti Password',
        ]);
    }

    /**
     * Get security alert title based on type
     */
    private function getSecurityAlertTitle(string $type): string
    {
        return match($type) {
            'failed_login_attempts' => 'Percobaan Login Gagal',
            'new_location_login' => 'Login dari Lokasi Baru',
            'suspicious_activity' => 'Aktivitas Mencurigakan',
            'account_locked' => 'Akun Dikunci',
            default => 'Pemberitahuan Keamanan',
        };
    }

    /**
     * Format security alert message
     */
    private function formatSecurityAlertMessage(array $alertData): string
    {
        return match($alertData['type']) {
            'failed_login_attempts' => "Terdeteksi {$alertData['attempt_count']} percobaan login gagal pada akun Anda dari IP {$alertData['ip']} ({$alertData['location']}) pada {$alertData['timestamp']->format('d/m/Y H:i')}.",
            'new_location_login' => "Login berhasil dari lokasi baru: {$alertData['location']} (IP: {$alertData['ip']}) pada {$alertData['timestamp']->format('d/m/Y H:i')}.",
            'suspicious_activity' => "Terdeteksi aktivitas mencurigakan pada akun Anda. Jika ini bukan Anda, segera ganti password.",
            'account_locked' => "Akun Anda telah dikunci sementara karena terlalu banyak percobaan login gagal.",
            default => 'Terdapat aktivitas keamanan pada akun Anda.',
        };
    }

    /**
     * Determine if security email should be sent
     */
    private function shouldSendSecurityEmail(string $type): bool
    {
        return in_array($type, [
            'failed_login_attempts',
            'account_locked',
            'suspicious_activity',
        ]);
    }

    /**
     * Get user notification preferences
     */
    public function getUserNotificationPreferences(User $user): array
    {
        // In real implementation, this would come from user settings
        return [
            'security_alerts' => true,
            'login_notifications' => true,
            'email_notifications' => true,
            'sms_notifications' => false,
        ];
    }
}

/**
 * Enhanced Security Service dengan threat intelligence
 */
class SecurityService
{
    private const THREAT_SCORE_THRESHOLD = 75;
    private const HIGH_RISK_COUNTRIES = ['CN', 'RU', 'KP', 'IR'];
    private const SUSPICIOUS_USER_AGENTS = [
        'bot', 'crawler', 'spider', 'scraper', 'curl', 'wget'
    ];

    /**
     * Calculate risk score for login attempt
     */
    public function calculateRiskScore(array $loginData): int
    {
        $score = 0;

        // IP reputation check
        $score += $this->checkIpReputation($loginData['ip']);

        // Geographic risk
        $score += $this->checkGeographicRisk($loginData['location'] ?? []);

        // User agent analysis
        $score += $this->analyzeUserAgent($loginData['user_agent'] ?? '');

        // Time-based analysis
        $score += $this->analyzeLoginTime($loginData['timestamp'] ?? now());

        // Failed attempt history
        $score += $this->checkFailureHistory($loginData['ip'], $loginData['email'] ?? '');

        // Account-specific risk
        if (isset($loginData['user'])) {
            $score += $this->checkAccountRisk($loginData['user']);
        }

        return min($score, 100); // Cap at 100
    }

    /**
     * Check IP reputation
     */
    private function checkIpReputation(string $ip): int
    {
        $score = 0;

        // Check if IP is in known threat lists
        $threatKey = "threat_intel:{$ip}";
        $threatData = Cache::get($threatKey);

        if ($threatData) {
            $score += min($threatData['activity_count'] * 5, 30);
            
            if (in_array('critical', $threatData['severity_levels'] ?? [])) {
                $score += 25;
            }
        }

        // Check if IP was recently blocked
        if (Cache::has("recently_blocked:{$ip}")) {
            $score += 20;
        }

        return $score;
    }

    /**
     * Check geographic risk factors
     */
    private function checkGeographicRisk(array $location): int
    {
        $score = 0;

        // High-risk countries
        if (in_array($location['country'] ?? '', self::HIGH_RISK_COUNTRIES)) {
            $score += 30;
        }

        // Unknown/masked location
        if (($location['city'] ?? 'UNKNOWN') === 'UNKNOWN' || 
            ($location['province'] ?? 'UNKNOWN') === 'UNKNOWN') {
            $score += 15;
        }

        return $score;
    }

    /**
     * Analyze user agent for suspicious patterns
     */
    private function analyzeUserAgent(string $userAgent): int
    {
        $score = 0;
        $userAgent = strtolower($userAgent);

        // Check for suspicious user agents
        foreach (self::SUSPICIOUS_USER_AGENTS as $suspicious) {
            if (str_contains($userAgent, $suspicious)) {
                $score += 25;
                break;
            }
        }

        // Very short or empty user agent
        if (strlen($userAgent) < 20) {
            $score += 15;
        }

        // Common attack tools
        if (preg_match('/python|java|go-http|libwww|curl/i', $userAgent)) {
            $score += 20;
        }

        return $score;
    }

    /**
     * Analyze login time patterns
     */
    private function analyzeLoginTime(\Carbon\Carbon $timestamp): int
    {
        $score = 0;
        $hour = $timestamp->hour;

        // Unusual hours (2-6 AM local time)
        if ($hour >= 2 && $hour <= 6) {
            $score += 10;
        }

        // Weekend activity for business accounts might be suspicious
        if ($timestamp->isWeekend()) {
            $score += 5;
        }

        return $score;
    }

    /**
     * Check failure history for IP and email
     */
    private function checkFailureHistory(string $ip, string $email): int
    {
        $score = 0;

        // Check recent failures from this IP
        $ipFailures = Cache::get("failed_attempts:{$ip}", 0);
        $score += min($ipFailures * 5, 25);

        // Check if this email had failures from other IPs
        $emailFailures = Cache::get("email_failures:{$email}", 0);
        $score += min($emailFailures * 3, 15);

        return $score;
    }

    /**
     * Check account-specific risk factors
     */
    private function checkAccountRisk(User $user): int
    {
        $score = 0;

        // High-privilege accounts get higher scrutiny
        if ($user->hasRoleType(['superadmin', 'admin'])) {
            $score += 10;
        }

        // Recently created accounts
        if ($user->created_at->diffInDays() < 7) {
            $score += 15;
        }

        // Account with recent security incidents
        $incidentKey = "security_incidents:{$user->id}";
        if (Cache::has($incidentKey)) {
            $score += 20;
        }

        return $score;
    }

    /**
     * Determine if login should be blocked based on risk score
     */
    public function shouldBlockLogin(int $riskScore): bool
    {
        return $riskScore >= self::THREAT_SCORE_THRESHOLD;
    }

    /**
     * Get risk level description
     */
    public function getRiskLevel(int $score): string
    {
        return match(true) {
            $score >= 80 => 'critical',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            $score >= 20 => 'low',
            default => 'minimal'
        };
    }

    /**
     * Update threat intelligence based on login attempt
     */
    public function updateThreatIntelligence(string $ip, array $data): void
    {
        $threatKey = "threat_intel:{$ip}";
        $threatData = Cache::get($threatKey, [
            'first_seen' => now()->toISOString(),
            'activity_count' => 0,
            'risk_indicators' => [],
            'countries' => [],
            'user_agents' => [],
        ]);

        $threatData['last_seen'] = now()->toISOString();
        $threatData['activity_count']++;

        // Track countries
        if (isset($data['location']['country'])) {
            $country = $data['location']['country'];
            $threatData['countries'][$country] = ($threatData['countries'][$country] ?? 0) + 1;
        }

        // Track user agents
        if (isset($data['user_agent'])) {
            $userAgent = substr($data['user_agent'], 0, 100);
            $threatData['user_agents'][] = $userAgent;
            $threatData['user_agents'] = array_slice(array_unique($threatData['user_agents']), -10);
        }

        Cache::put($threatKey, $threatData, now()->addDays(30));
    }

    /**
     * Get comprehensive security report
     */
    public function getSecurityReport(int $days = 7): array
    {
        $endDate = now();
        $startDate = $endDate->copy()->subDays($days);
        
        $report = [];
        
        // Collect daily metrics
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $dateKey = $date->format('Y-m-d');
            
            $report['daily_metrics'][$dateKey] = [
                'date' => $dateKey,
                'alerts' => Cache::get("security_alert_metrics:{$dateKey}", []),
                'blocked_ips' => Cache::get("security_metrics:{$dateKey}", [])['blocked_ips'] ?? 0,
                'failed_logins' => $this->getFailedLoginCount($dateKey),
            ];
        }

        // Top threat IPs
        $report['top_threat_ips'] = $this->getTopThreatIps(10);
        
        // Recent critical activities
        $report['recent_critical_activities'] = $this->getRecentCriticalActivities(20);
        
        return $report;
    }

    /**
     * Get failed login count for specific date
     */
    private function getFailedLoginCount(string $date): int
    {
        // This would typically query the database
        return Cache::get("failed_login_count:{$date}", 0);
    }

    /**
     * Get top threat IPs
     */
    private function getTopThreatIps(int $limit): array
    {
        // This would collect from threat intelligence cache
        $threats = [];
        // Implementation would scan threat_intel:* keys
        return array_slice($threats, 0, $limit);
    }

    /**
     * Get recent critical activities
     */
    private function getRecentCriticalActivities(int $limit): array
    {
        $activities = [];
        // Implementation would collect from suspicious_activities:* keys
        return array_slice($activities, 0, $limit);
    }
}