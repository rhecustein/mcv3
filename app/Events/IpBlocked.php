<?php

namespace App\Events;

use App\Models\IpLock;
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

class IpBlocked implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $ipAddress;
    public ?User $targetUser;
    public ?User $blockedBy;
    public IpLock $ipLock;
    public string $reason;
    public string $lockType;
    public ?Carbon $expiresAt;
    public Carbon $blockedAt;
    public string $eventId;
    public array $geoLocation;
    public array $relatedActivity;
    public string $severity;

    /**
     * Create a new event instance.
     */
    public function __construct(
        string $ipAddress,
        ?User $blockedBy = null,
        IpLock $ipLock = null,
        ?User $targetUser = null
    ) {
        $this->ipAddress = $ipAddress;
        $this->blockedBy = $blockedBy;
        $this->targetUser = $targetUser;
        $this->ipLock = $ipLock;
        $this->reason = $ipLock->reason ?? 'Security violation';
        $this->lockType = $ipLock->lock_type ?? 'temporary';
        $this->expiresAt = $ipLock->unlocked_at;
        $this->blockedAt = $ipLock->locked_at ?? now();
        $this->eventId = 'ip_blocked_' . uniqid();
        $this->geoLocation = $this->resolveGeoLocation();
        $this->relatedActivity = $this->getRelatedActivity();
        $this->severity = $this->calculateSeverity();

        // Log the IP blocking event immediately
        $this->logIpBlocking();
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        return [
            // Global security channel for all security personnel
            new PrivateChannel('security-global'),
            
            // IP monitoring channel for network administrators
            new PrivateChannel('ip-monitoring'),
            
            // Admin security channel for system administrators
            new PrivateChannel('admin-security'),
            
            // Regional security channel (if geo-location available)
            new PrivateChannel('security-region.' . $this->getRegionCode()),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'ip.blocked';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'event_id' => $this->eventId,
            'ip_address' => $this->ipAddress,
            'lock_type' => $this->lockType,
            'reason' => $this->reason,
            'severity' => $this->severity,
            'blocked_at' => $this->blockedAt->toISOString(),
            'expires_at' => $this->expiresAt?->toISOString(),
            'duration_minutes' => $this->getDurationInMinutes(),
            'blocked_by' => $this->getBlockedByInfo(),
            'target_user' => $this->getTargetUserInfo(),
            'geo_location' => $this->geoLocation,
            'related_activity' => $this->relatedActivity,
            'threat_level' => $this->getThreatLevel(),
            'recommended_actions' => $this->getRecommendedActions(),
            'statistics' => $this->getIpStatistics(),
        ];
    }

    /**
     * Determine if this event should be broadcast immediately.
     */
    public function shouldBroadcast(): bool
    {
        // Always broadcast IP blocking events as they are critical security events
        return true;
    }

    /**
     * Log the IP blocking event with comprehensive details.
     */
    private function logIpBlocking(): void
    {
        $logLevel = match ($this->severity) {
            'critical' => 'critical',
            'high' => 'alert',
            'medium' => 'warning',
            'low' => 'notice',
            default => 'warning',
        };

        Log::log($logLevel, '🚫 IP Address Blocked', [
            'event_id' => $this->eventId,
            'ip_address' => $this->ipAddress,
            'lock_type' => $this->lockType,
            'reason' => $this->reason,
            'severity' => $this->severity,
            'blocked_at' => $this->blockedAt->toISOString(),
            'expires_at' => $this->expiresAt?->toISOString(),
            'duration_minutes' => $this->getDurationInMinutes(),
            'blocked_by' => [
                'id' => $this->blockedBy?->id,
                'email' => $this->blockedBy?->email,
                'role' => $this->blockedBy?->role_type,
                'action_type' => $this->blockedBy ? 'manual' : 'automatic',
            ],
            'target_user' => [
                'id' => $this->targetUser?->id,
                'email' => $this->targetUser?->email,
                'role' => $this->targetUser?->role_type,
            ],
            'geo_location' => $this->geoLocation,
            'related_activity' => $this->relatedActivity,
            'threat_assessment' => [
                'threat_level' => $this->getThreatLevel(),
                'risk_factors' => $this->getRiskFactors(),
            ],
            'system_impact' => $this->getSystemImpact(),
        ]);
    }

    /**
     * Resolve geographic location information for the IP.
     */
    private function resolveGeoLocation(): array
    {
        try {
            // Try to get location from cache first
            if ($cached = cache("geo_location:{$this->ipAddress}")) {
                return $cached;
            }

            // Use IP2Location service if available
            if (app()->bound('App\Services\IP2LocationService')) {
                $locationService = app('App\Services\IP2LocationService');
                $location = $locationService->getLocation($this->ipAddress);
                
                $geoData = [
                    'country' => $location['country'] ?? 'Unknown',
                    'province' => $location['province'] ?? 'Unknown',
                    'city' => $location['city'] ?? 'Unknown',
                    'latitude' => $location['latitude'] ?? null,
                    'longitude' => $location['longitude'] ?? null,
                    'timezone' => $location['timezone'] ?? null,
                    'isp' => $location['isp'] ?? 'Unknown',
                    'organization' => $location['organization'] ?? 'Unknown',
                ];

                // Cache for 1 hour
                cache(["geo_location:{$this->ipAddress}" => $geoData], 3600);
                return $geoData;
            }

            return ['status' => 'location_service_unavailable'];

        } catch (\Exception $e) {
            Log::warning('Failed to resolve geo location for IP', [
                'ip' => $this->ipAddress,
                'error' => $e->getMessage(),
            ]);

            return ['status' => 'resolution_failed', 'error' => $e->getMessage()];
        }
    }

    /**
     * Get related security activity for this IP.
     */
    private function getRelatedActivity(): array
    {
        try {
            // Get recent login attempts from this IP
            $recentLogins = \App\Models\SessionLogin::where('ip_address', $this->ipAddress)
                ->where('logged_in_at', '>=', now()->subDays(7))
                ->orderBy('logged_in_at', 'desc')
                ->take(10)
                ->get(['user_id', 'success', 'logged_in_at', 'user_agent']);

            // Get failed attempts count
            $failedAttempts = $recentLogins->where('success', false)->count();
            $successfulLogins = $recentLogins->where('success', true)->count();

            // Get unique users who tried to access from this IP
            $uniqueUsers = $recentLogins->pluck('user_id')->unique()->count();

            // Get unique user agents (potential device fingerprinting)
            $uniqueUserAgents = $recentLogins->pluck('user_agent')->unique()->count();

            return [
                'recent_login_attempts' => $recentLogins->count(),
                'failed_attempts' => $failedAttempts,
                'successful_logins' => $successfulLogins,
                'unique_users_targeted' => $uniqueUsers,
                'unique_devices' => $uniqueUserAgents,
                'first_seen' => $recentLogins->last()?->logged_in_at?->toISOString(),
                'last_seen' => $recentLogins->first()?->logged_in_at?->toISOString(),
                'attack_pattern' => $this->analyzeAttackPattern($recentLogins),
            ];

        } catch (\Exception $e) {
            Log::warning('Failed to get related activity for IP', [
                'ip' => $this->ipAddress,
                'error' => $e->getMessage(),
            ]);

            return ['status' => 'analysis_failed'];
        }
    }

    /**
     * Analyze attack patterns from login attempts.
     */
    private function analyzeAttackPattern($loginAttempts): array
    {
        if ($loginAttempts->isEmpty()) {
            return ['pattern' => 'no_data'];
        }

        $failedAttempts = $loginAttempts->where('success', false);
        $totalAttempts = $loginAttempts->count();
        $failureRate = $totalAttempts > 0 ? ($failedAttempts->count() / $totalAttempts) : 0;

        // Analyze timing patterns
        $timingPattern = 'normal';
        if ($totalAttempts > 5) {
            $attempts = $loginAttempts->sortBy('logged_in_at');
            $intervals = [];
            
            for ($i = 1; $i < $attempts->count(); $i++) {
                $current = Carbon::parse($attempts[$i]->logged_in_at);
                $previous = Carbon::parse($attempts[$i-1]->logged_in_at);
                $intervals[] = $current->diffInSeconds($previous);
            }

            $avgInterval = array_sum($intervals) / count($intervals);
            
            if ($avgInterval < 10) {
                $timingPattern = 'rapid_fire';
            } elseif ($avgInterval < 60) {
                $timingPattern = 'automated';
            } elseif ($avgInterval > 3600) {
                $timingPattern = 'slow_persistent';
            }
        }

        return [
            'pattern' => $this->classifyAttackPattern($failureRate, $timingPattern, $totalAttempts),
            'failure_rate' => round($failureRate * 100, 2),
            'timing_pattern' => $timingPattern,
            'intensity' => $this->calculateAttackIntensity($totalAttempts, $failureRate),
        ];
    }

    /**
     * Classify the type of attack pattern.
     */
    private function classifyAttackPattern(float $failureRate, string $timingPattern, int $totalAttempts): string
    {
        if ($failureRate > 0.8 && $totalAttempts > 10) {
            return match ($timingPattern) {
                'rapid_fire' => 'brute_force_rapid',
                'automated' => 'brute_force_automated',
                'slow_persistent' => 'credential_stuffing',
                default => 'brute_force_generic',
            };
        }

        if ($failureRate > 0.5 && $totalAttempts > 5) {
            return 'password_spraying';
        }

        if ($failureRate < 0.2 && $totalAttempts > 3) {
            return 'legitimate_with_errors';
        }

        return 'reconnaissance';
    }

    /**
     * Calculate attack intensity level.
     */
    private function calculateAttackIntensity(int $attempts, float $failureRate): string
    {
        $score = ($attempts * 2) + ($failureRate * 50);

        return match (true) {
            $score >= 100 => 'critical',
            $score >= 50 => 'high',
            $score >= 20 => 'medium',
            default => 'low',
        };
    }

    /**
     * Calculate overall severity of the IP blocking event.
     */
    private function calculateSeverity(): string
    {
        $score = 0;

        // Lock type factor
        $score += $this->lockType === 'permanent' ? 40 : 20;

        // Reason-based scoring
        if (str_contains(strtolower($this->reason), 'brute force')) {
            $score += 30;
        } elseif (str_contains(strtolower($this->reason), 'suspicious')) {
            $score += 25;
        } elseif (str_contains(strtolower($this->reason), 'automated')) {
            $score += 20;
        }

        // User role factor (if target user exists)
        if ($this->targetUser) {
            if ($this->targetUser->role_type === 'superadmin') {
                $score += 50;
            } elseif ($this->targetUser->role_type === 'admin') {
                $score += 30;
            }
        }

        // Geographic factor
        if (isset($this->geoLocation['country']) && $this->geoLocation['country'] !== 'Indonesia') {
            $score += 15;
        }

        return match (true) {
            $score >= 80 => 'critical',
            $score >= 60 => 'high',
            $score >= 40 => 'medium',
            default => 'low',
        };
    }

    /**
     * Get duration in minutes for temporary blocks.
     */
    private function getDurationInMinutes(): ?int
    {
        if ($this->lockType === 'permanent' || !$this->expiresAt) {
            return null;
        }

        return $this->blockedAt->diffInMinutes($this->expiresAt);
    }

    /**
     * Get information about who blocked the IP.
     */
    private function getBlockedByInfo(): array
    {
        if (!$this->blockedBy) {
            return [
                'type' => 'automatic',
                'system' => 'security_service',
                'trigger' => 'automated_security_policy',
            ];
        }

        return [
            'type' => 'manual',
            'user_id' => $this->blockedBy->id,
            'name' => $this->blockedBy->name,
            'email' => $this->blockedBy->email,
            'role' => $this->blockedBy->role_type,
        ];
    }

    /**
     * Get information about the target user (if any).
     */
    private function getTargetUserInfo(): ?array
    {
        if (!$this->targetUser) {
            return null;
        }

        return [
            'id' => $this->targetUser->id,
            'name' => $this->targetUser->name,
            'email' => $this->targetUser->email,
            'role' => $this->targetUser->role_type,
            'created_at' => $this->targetUser->created_at->toISOString(),
            'last_login' => $this->targetUser->last_login_at?->toISOString(),
            'is_active' => $this->targetUser->is_active ?? true,
        ];
    }

    /**
     * Get threat level assessment.
     */
    private function getThreatLevel(): string
    {
        $factors = [];

        // Check for high-value target
        if ($this->targetUser && in_array($this->targetUser->role_type, ['superadmin', 'admin'])) {
            $factors[] = 'high_value_target';
        }

        // Check for foreign origin
        if (isset($this->geoLocation['country']) && $this->geoLocation['country'] !== 'Indonesia') {
            $factors[] = 'foreign_origin';
        }

        // Check for attack intensity
        if (isset($this->relatedActivity['attack_pattern']['intensity'])) {
            $intensity = $this->relatedActivity['attack_pattern']['intensity'];
            if (in_array($intensity, ['high', 'critical'])) {
                $factors[] = 'high_intensity_attack';
            }
        }

        // Check for persistence
        if (isset($this->relatedActivity['recent_login_attempts']) && 
            $this->relatedActivity['recent_login_attempts'] > 20) {
            $factors[] = 'persistent_attacker';
        }

        $threatScore = count($factors) * 25;

        return match (true) {
            $threatScore >= 75 => 'critical',
            $threatScore >= 50 => 'high', 
            $threatScore >= 25 => 'medium',
            default => 'low',
        };
    }

    /**
     * Get recommended security actions.
     */
    private function getRecommendedActions(): array
    {
        $actions = [
            'immediate' => [],
            'short_term' => [],
            'long_term' => [],
        ];

        // Immediate actions based on severity
        if ($this->severity === 'critical') {
            $actions['immediate'][] = 'Investigate immediately';
            $actions['immediate'][] = 'Check for signs of successful breach';
            $actions['immediate'][] = 'Consider blocking entire IP range';
        }

        // Actions based on threat level
        $threatLevel = $this->getThreatLevel();
        if ($threatLevel === 'high' || $threatLevel === 'critical') {
            $actions['immediate'][] = 'Alert security team';
            $actions['immediate'][] = 'Review recent successful logins from this IP';
        }

        // Actions based on attack pattern
        if (isset($this->relatedActivity['attack_pattern']['pattern'])) {
            $pattern = $this->relatedActivity['attack_pattern']['pattern'];
            
            if (str_contains($pattern, 'brute_force')) {
                $actions['immediate'][] = 'Check password policies';
                $actions['short_term'][] = 'Implement additional rate limiting';
            }
            
            if ($pattern === 'credential_stuffing') {
                $actions['immediate'][] = 'Force password reset for targeted accounts';
                $actions['short_term'][] = 'Enable 2FA for affected users';
            }
        }

        // Geographic-based actions
        if (isset($this->geoLocation['country']) && $this->geoLocation['country'] !== 'Indonesia') {
            $actions['short_term'][] = 'Review geographic access policies';
            $actions['long_term'][] = 'Consider geo-blocking for sensitive accounts';
        }

        // Default actions
        $actions['short_term'][] = 'Monitor for similar attacks from related IPs';
        $actions['long_term'][] = 'Review and update security policies';

        return $actions;
    }

    /**
     * Get IP-specific statistics.
     */
    private function getIpStatistics(): array
    {
        return [
            'total_blocks' => IpLock::where('ip_address', $this->ipAddress)->count(),
            'active_blocks' => IpLock::where('ip_address', $this->ipAddress)->active()->count(),
            'first_blocked' => IpLock::where('ip_address', $this->ipAddress)
                ->oldest('locked_at')
                ->value('locked_at'),
            'block_frequency' => $this->calculateBlockFrequency(),
        ];
    }

    /**
     * Calculate how frequently this IP gets blocked.
     */
    private function calculateBlockFrequency(): string
    {
        $blocks = IpLock::where('ip_address', $this->ipAddress)
            ->where('locked_at', '>=', now()->subDays(30))
            ->count();

        return match (true) {
            $blocks >= 10 => 'very_frequent',
            $blocks >= 5 => 'frequent',
            $blocks >= 2 => 'occasional',
            default => 'rare',
        };
    }

    /**
     * Get risk factors for this IP block.
     */
    private function getRiskFactors(): array
    {
        $factors = [];

        if ($this->lockType === 'permanent') {
            $factors[] = 'permanent_block';
        }

        if ($this->targetUser && $this->targetUser->role_type === 'superadmin') {
            $factors[] = 'admin_account_targeted';
        }

        if (isset($this->geoLocation['country']) && $this->geoLocation['country'] !== 'Indonesia') {
            $factors[] = 'foreign_ip';
        }

        if (isset($this->relatedActivity['failed_attempts']) && 
            $this->relatedActivity['failed_attempts'] > 10) {
            $factors[] = 'high_failure_rate';
        }

        return $factors;
    }

    /**
     * Assess system impact of this IP block.
     */
    private function getSystemImpact(): array
    {
        return [
            'users_potentially_affected' => $this->relatedActivity['unique_users_targeted'] ?? 0,
            'services_protected' => ['authentication', 'user_accounts'],
            'performance_impact' => 'minimal',
            'availability_impact' => $this->lockType === 'permanent' ? 'permanent_restriction' : 'temporary_restriction',
        ];
    }

    /**
     * Get region code for geographic broadcasting.
     */
    private function getRegionCode(): string
    {
        $province = $this->geoLocation['province'] ?? 'unknown';
        return strtolower(str_replace(' ', '_', $province));
    }

    /**
     * Convert the event to array for storage or API responses.
     */
    public function toArray(): array
    {
        return [
            'event_id' => $this->eventId,
            'ip_address' => $this->ipAddress,
            'lock_type' => $this->lockType,
            'reason' => $this->reason,
            'severity' => $this->severity,
            'blocked_at' => $this->blockedAt->toISOString(),
            'expires_at' => $this->expiresAt?->toISOString(),
            'blocked_by' => $this->getBlockedByInfo(),
            'target_user' => $this->getTargetUserInfo(),
            'geo_location' => $this->geoLocation,
            'related_activity' => $this->relatedActivity,
            'threat_level' => $this->getThreatLevel(),
            'recommended_actions' => $this->getRecommendedActions(),
            'statistics' => $this->getIpStatistics(),
        ];
    }
}