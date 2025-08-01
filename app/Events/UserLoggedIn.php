<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

/**
 * Event untuk login berhasil
 */
class UserLoggedIn
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public string $ipAddress;
    public array $locationData;
    public string $sessionId;
    public string $userAgent;

    public function __construct(User $user, string $ipAddress, array $locationData)
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->locationData = $locationData;
        $this->sessionId = session()->getId();
        $this->userAgent = request()->userAgent();
    }

    public function getLocationString(): string
    {
        $parts = array_filter([
            $this->locationData['city'] ?? null,
            $this->locationData['province'] ?? null,
            $this->locationData['country'] ?? null,
        ]);

        return implode(', ', $parts) ?: 'Unknown Location';
    }
}

/**
 * Event untuk IP yang diblokir
 */
class IpBlocked
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $ipAddress;
    public ?User $user;
    public \App\Models\IpLock $ipLock;
    public array $additionalData;

    public function __construct(string $ipAddress, ?User $user, \App\Models\IpLock $ipLock, array $additionalData = [])
    {
        $this->ipAddress = $ipAddress;
        $this->user = $user;
        $this->ipLock = $ipLock;
        $this->additionalData = $additionalData;
    }

    public function isTemporary(): bool
    {
        return $this->ipLock->lock_type === 'temporary';
    }

    public function getBlockDuration(): ?int
    {
        if (!$this->isTemporary() || !$this->ipLock->unlocked_at) {
            return null;
        }

        return $this->ipLock->unlocked_at->diffInMinutes(now());
    }
}

/**
 * Event untuk aktivitas mencurigakan
 */
class SuspiciousActivity
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public ?User $user;
    public string $ipAddress;
    public string $activityType;
    public array $context;
    public string $severity;

    public function __construct(?User $user, string $ipAddress, string $activityType, array $context = [], string $severity = 'medium')
    {
        $this->user = $user;
        $this->ipAddress = $ipAddress;
        $this->activityType = $activityType;
        $this->context = $context;
        $this->severity = $severity;
    }

    public function isCritical(): bool
    {
        return $this->severity === 'critical';
    }

    public function isHighSeverity(): bool
    {
        return in_array($this->severity, ['high', 'critical']);
    }

    public function getActivityDescription(): string
    {
        return match($this->activityType) {
            'multiple_account_targeting' => 'Multiple account login attempts from same IP',
            'high_login_attempt_rate' => 'High frequency login attempts',
            'geographic_restriction_violation' => 'Login attempt from restricted geographic location',
            'system_error_during_login' => 'System error occurred during login process',
            'unusual_user_agent' => 'Login attempt with unusual user agent',
            'concurrent_session_limit_exceeded' => 'Too many concurrent sessions detected',
            default => 'Suspicious activity detected',
        };
    }
}

/**
 * Enhanced UserLoginFailed Event
 */
class UserLoginFailedEnhanced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $email;
    public string $ipAddress;
    public string $userAgent;
    public array $locationData;
    public int $attemptCount;
    public ?User $user;
    public string $failureReason;
    public array $additionalContext;

    public function __construct(
        string $email,
        string $ipAddress,
        string $userAgent,
        array $locationData = [],
        int $attemptCount = 1,
        ?User $user = null,
        string $failureReason = 'Authentication failed',
        array $additionalContext = []
    ) {
        $this->email = $email;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->locationData = $locationData;
        $this->attemptCount = $attemptCount;
        $this->user = $user;
        $this->failureReason = $failureReason;
        $this->additionalContext = $additionalContext;
    }

    public function getLocationString(): string
    {
        $parts = array_filter([
            $this->locationData['city'] ?? null,
            $this->locationData['province'] ?? null,
            $this->locationData['country'] ?? null,
        ]);

        return implode(', ', $parts) ?: 'Unknown Location';
    }

    public function isCriticalAttempt(): bool
    {
        return $this->attemptCount >= 5;
    }

    public function isHighRiskAttempt(): bool
    {
        return $this->attemptCount >= 3;
    }

    public function getUserId(): ?int
    {
        return $this->user?->id;
    }

    public function getFailureContext(): array
    {
        return array_merge([
            'email' => $this->email,
            'ip_address' => $this->ipAddress,
            'location' => $this->getLocationString(),
            'attempt_count' => $this->attemptCount,
            'failure_reason' => $this->failureReason,
            'timestamp' => now()->toISOString(),
        ], $this->additionalContext);
    }

    public function isFromUnknownLocation(): bool
    {
        return ($this->locationData['city'] ?? 'UNKNOWN') === 'UNKNOWN' ||
               ($this->locationData['province'] ?? 'UNKNOWN') === 'UNKNOWN';
    }
}