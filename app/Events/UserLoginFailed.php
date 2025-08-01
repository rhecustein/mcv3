<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserLoginFailed
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $email;
    public string $ipAddress;
    public string $userAgent;
    public array $locationData;
    public int $attemptCount;
    public ?\App\Models\User $user;

    /**
     * Create a new event instance.
     *
     * @param string $email
     * @param string $ipAddress
     * @param string $userAgent
     * @param array $locationData
     * @param int $attemptCount
     * @param \App\Models\User|null $user
     */
    public function __construct(
        string $email,
        string $ipAddress,
        string $userAgent,
        array $locationData = [],
        int $attemptCount = 1,
        ?\App\Models\User $user = null
    ) {
        $this->email = $email;
        $this->ipAddress = $ipAddress;
        $this->userAgent = $userAgent;
        $this->locationData = $locationData;
        $this->attemptCount = $attemptCount;
        $this->user = $user;
    }

    /**
     * Get location data as string for logging.
     */
    public function getLocationString(): string
    {
        $parts = array_filter([
            $this->locationData['city'] ?? null,
            $this->locationData['province'] ?? null,
            $this->locationData['country'] ?? null,
        ]);

        return implode(', ', $parts) ?: 'Unknown Location';
    }

    /**
     * Check if this is a critical attempt (5+ failures).
     */
    public function isCriticalAttempt(): bool
    {
        return $this->attemptCount >= 5;
    }

    /**
     * Get user ID if user exists.
     */
    public function getUserId(): ?int
    {
        return $this->user?->id;
    }
}