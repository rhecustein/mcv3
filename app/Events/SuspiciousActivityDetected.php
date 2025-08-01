<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class SuspiciousActivityDetected implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $activityType;
    public array $details;
    public string $severity;
    public string $ipAddress;
    public string $userAgent;
    public Carbon $detectedAt;
    public string $eventId;

    /**
     * Create a new event instance.
     */
    public function __construct(
        User $user,
        string $activityType,
        array $details = [],
        string $severity = 'medium'
    ) {
        $this->user = $user;
        $this->activityType = $activityType;
        $this->details = $details;
        $this->severity = $severity;
        $this->ipAddress = request()->ip() ?? 'unknown';
        $this->userAgent = request()->userAgent() ?? 'unknown';
        $this->detectedAt = now();
        $this->eventId = 'suspicious_' . uniqid();

        // Log the suspicious activity immediately
        $this->logSuspiciousActivity();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Private channel for security team
            new PrivateChannel('security-alerts'),
            
            // Private channel for specific admin monitoring
            new PrivateChannel('admin-security'),
            
            // User-specific channel (if needed for notifications)
            new PrivateChannel('user-security.' . $this->user->id),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'suspicious.activity.detected';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_id' => $this->eventId,
            'user' => [
                'id' => $this->user->id,
                'email' => $this->user->email,
                'name' => $this->user->name,
                'role' => $this->user->role_type,
            ],
            'activity_type' => $this->activityType,
            'severity' => $this->severity,
            'detected_at' => $this->detectedAt->toISOString(),
            'ip_address' => $this->ipAddress,
            'location' => $this->getLocationInfo(),
            'summary' => $this->generateSummary(),
            'recommended_actions' => $this->getRecommendedActions(),
        ];
    }

    /**
     * Determine if this event should be broadcast immediately.
     */
    public function shouldBroadcast(): bool
    {
        // Only broadcast high severity events or specific activity types
        return in_array($this->severity, ['high', 'critical']) || 
               in_array($this->activityType, ['login_pattern', 'geographic_violation', 'brute_force']);
    }

    /**
     * Log the suspicious activity with detailed information.
     */
    private function logSuspiciousActivity(): void
    {
        $logLevel = match ($this->severity) {
            'critical' => 'critical',
            'high' => 'alert',
            'medium' => 'warning',
            'low' => 'notice',
            default => 'warning',
        };

        Log::log($logLevel, '🚨 Suspicious Activity Detected', [
            'event_id' => $this->eventId,
            'user_id' => $this->user->id,
            'email' => $this->user->email,
            'role' => $this->user->role_type,
            'activity_type' => $this->activityType,
            'severity' => $this->severity,
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'detected_at' => $this->detectedAt->toISOString(),
            'details' => $this->details,
            'location_info' => $this->getLocationInfo(),
            'risk_indicators' => $this->getRiskIndicators(),
            'context' => $this->getContextualInfo(),
        ]);
    }

    /**
     * Get location information from the request.
     */
    private function getLocationInfo(): array
    {
        return [
            'city' => $this->details['location']['city'] ?? $this->details['city'] ?? 'Unknown',
            'province' => $this->details['location']['province'] ?? $this->details['province'] ?? 'Unknown',
            'country' => $this->details['location']['country'] ?? 'Indonesia',
            'latitude' => $this->details['location']['latitude'] ?? null,
            'longitude' => $this->details['location']['longitude'] ?? null,
        ];
    }

    /**
     * Generate a human-readable summary of the suspicious activity.
     */
    private function generateSummary(): string
    {
        return match ($this->activityType) {
            'login_pattern' => $this->generateLoginPatternSummary(),
            'geographic_violation' => $this->generateGeographicViolationSummary(),
            'brute_force' => $this->generateBruteForceAttackSummary(),
            'session_anomaly' => $this->generateSessionAnomalySummary(),
            'privilege_escalation' => $this->generatePrivilegeEscalationSummary(),
            'data_access_anomaly' => $this->generateDataAccessAnomalySummary(),
            'multiple_device_access' => $this->generateMultipleDeviceAccessSummary(),
            default => "Suspicious activity of type '{$this->activityType}' detected for user {$this->user->email}",
        };
    }

    /**
     * Generate summary for login pattern anomalies.
     */
    private function generateLoginPatternSummary(): string
    {
        $riskScore = $this->details['risk_score'] ?? 0;
        $factors = $this->details['factors'] ?? [];
        
        $summary = "Unusual login pattern detected for {$this->user->email} (Risk Score: {$riskScore}).";
        
        if (!empty($factors)) {
            $factorDescriptions = [
                'multiple_failed_attempts' => 'multiple failed login attempts',
                'new_location' => 'login from new geographic location',
                'unusual_time' => 'login at unusual time',
                'multiple_ips' => 'access from multiple IP addresses',
                'concurrent_different_locations' => 'concurrent sessions from different locations',
            ];
            
            $readableFactors = array_map(
                fn($factor) => $factorDescriptions[$factor] ?? $factor,
                $factors
            );
            
            $summary .= " Factors: " . implode(', ', $readableFactors) . ".";
        }
        
        return $summary;
    }

    /**
     * Generate summary for geographic violations.
     */
    private function generateGeographicViolationSummary(): string
    {
        $province = $this->details['province'] ?? 'Unknown';
        return "Geographic access violation: User {$this->user->email} attempted login from restricted province: {$province}.";
    }

    /**
     * Generate summary for brute force attacks.
     */
    private function generateBruteForceAttackSummary(): string
    {
        $attempts = $this->details['failed_attempts'] ?? 'multiple';
        return "Potential brute force attack detected: {$attempts} failed login attempts for {$this->user->email} from IP {$this->ipAddress}.";
    }

    /**
     * Generate summary for session anomalies.
     */
    private function generateSessionAnomalySummary(): string
    {
        return "Session anomaly detected for {$this->user->email}: unusual session behavior or multiple concurrent sessions from different locations.";
    }

    /**
     * Generate summary for privilege escalation attempts.
     */
    private function generatePrivilegeEscalationSummary(): string
    {
        return "Potential privilege escalation attempt detected for {$this->user->email}: unauthorized access to restricted resources.";
    }

    /**
     * Generate summary for data access anomalies.
     */
    private function generateDataAccessAnomalySummary(): string
    {
        return "Unusual data access pattern detected for {$this->user->email}: accessing resources outside normal behavior pattern.";
    }

    /**
     * Generate summary for multiple device access.
     */
    private function generateMultipleDeviceAccessSummary(): string
    {
        $deviceCount = $this->details['device_count'] ?? 'multiple';
        return "Multiple device access detected: {$this->user->email} is accessing from {$deviceCount} different devices simultaneously.";
    }

    /**
     * Get recommended actions based on activity type and severity.
     */
    private function getRecommendedActions(): array
    {
        $baseActions = [
            'Monitor user activity closely',
            'Review recent access logs',
        ];

        $specificActions = match ($this->activityType) {
            'login_pattern' => [
                'Verify user identity through alternative means',
                'Consider requiring additional authentication',
                'Review and potentially update user\'s security settings',
            ],
            'geographic_violation' => [
                'Block access from unauthorized locations',
                'Contact user to verify legitimate access attempt',
                'Update geographic access policies if needed',
            ],
            'brute_force' => [
                'Immediately block suspicious IP addresses',
                'Force password reset for affected account',
                'Enable additional security measures (2FA, CAPTCHA)',
            ],
            'session_anomaly' => [
                'Terminate suspicious sessions',
                'Require re-authentication for all active sessions',
                'Review session management policies',
            ],
            'privilege_escalation' => [
                'Immediately review user permissions',
                'Audit recent actions performed by the user',
                'Consider temporarily suspending account privileges',
            ],
            default => [
                'Investigate the specific nature of the suspicious activity',
                'Take appropriate action based on findings',
            ],
        };

        $severityActions = match ($this->severity) {
            'critical' => [
                'IMMEDIATE ACTION REQUIRED',
                'Consider emergency account lockdown',
                'Notify security team immediately',
            ],
            'high' => [
                'Escalate to security team',
                'Consider temporary account restrictions',
            ],
            'medium' => [
                'Schedule security review',
                'Implement additional monitoring',
            ],
            'low' => [
                'Log for future reference',
                'Continue normal monitoring',
            ],
            default => [],
        };

        return array_merge($baseActions, $specificActions, $severityActions);
    }

    /**
     * Get risk indicators for this activity.
     */
    private function getRiskIndicators(): array
    {
        $indicators = [];

        // Time-based indicators
        if (now()->hour < 6 || now()->hour > 22) {
            $indicators[] = 'off_hours_access';
        }

        // Location-based indicators
        if (($this->details['location']['province'] ?? '') !== 'Kepulauan Riau') {
            $indicators[] = 'external_location';
        }

        // Pattern-based indicators
        if (isset($this->details['risk_score']) && $this->details['risk_score'] > 50) {
            $indicators[] = 'high_risk_score';
        }

        // User behavior indicators
        if (isset($this->details['factors'])) {
            $indicators = array_merge($indicators, $this->details['factors']);
        }

        return array_unique($indicators);
    }

    /**
     * Get contextual information about the user and environment.
     */
    private function getContextualInfo(): array
    {
        return [
            'user_created_at' => $this->user->created_at->toISOString(),
            'user_last_login' => $this->user->last_login_at?->toISOString(),
            'user_role' => $this->user->role_type,
            'user_active' => $this->user->is_active ?? true,
            'session_info' => [
                'session_id' => session()->getId(),
                'session_started' => session()->get('_token'),
            ],
            'request_info' => [
                'method' => request()->method(),
                'url' => request()->fullUrl(),
                'referer' => request()->header('referer'),
            ],
            'environment' => [
                'app_env' => app()->environment(),
                'timestamp' => now()->toISOString(),
                'timezone' => config('app.timezone'),
            ],
        ];
    }

    /**
     * Convert the event to array for storage or API responses.
     */
    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'user_id' => $this->user->id,
            'user_email' => $this->user->email,
            'activity_type' => $this->activityType,
            'severity' => $this->severity,
            'detected_at' => $this->detectedAt->toISOString(),
            'ip_address' => $this->ipAddress,
            'user_agent' => $this->userAgent,
            'details' => $this->details,
            'location_info' => $this->getLocationInfo(),
            'summary' => $this->generateSummary(),
            'recommended_actions' => $this->getRecommendedActions(),
            'risk_indicators' => $this->getRiskIndicators(),
            'context' => $this->getContextualInfo(),
        ];
    }
}