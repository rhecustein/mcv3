<?php

namespace App\Models;

use App\Models\Traits\BelongsToTenant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes, BelongsToTenant;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'tenant_id',
        'name',
        'email',
        'password',
        'role_type',
        'phone',
        'avatar',
        'birth_date',
        'gender',
        'address',
        'last_ip',
        'last_login_at',
        'last_location',
        'last_activity_at',
        'otp_code',
        'otp_expires_at',
        'two_factor_enabled',
        'two_factor_secret',
        'is_active',
        'is_banned',
        'banned_at',
        'ban_reason',
        'timezone',
        'locale',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'birth_date' => 'date',
        'last_login_at' => 'datetime',
        'last_activity_at' => 'datetime',
        'otp_expires_at' => 'datetime',
        'banned_at' => 'datetime',
        'two_factor_enabled' => 'boolean',
        'is_active' => 'boolean',
        'is_banned' => 'boolean',
    ];

    // ================= NEW RELATIONSHIPS =================

    /**
     * Get the user's profile.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(UserProfile::class);
    }

    /**
     * Get the user's activity logs.
     */
    public function activityLogs(): HasMany
    {
        return $this->hasMany(ActivityLog::class);
    }

    /**
     * Get recent activity logs.
     */
    public function recentActivities(): HasMany
    {
        return $this->hasMany(ActivityLog::class)
                    ->latest()
                    ->limit(10);
    }

    // ================= EXISTING RELATIONSHIPS =================

    /**
     * Get the outlet if user is an outlet.
     */
    public function outlet(): HasOne
    {
        return $this->hasOne(Outlet::class);
    }

    /**
     * Get the admin if user is an admin.
     */
    public function admin(): HasOne
    {
        return $this->hasOne(Admin::class);
    }

    /**
     * Get the doctor if user is a doctor.
     */
    public function doctor(): HasOne
    {
        return $this->hasOne(Doctor::class);
    }

    /**
     * Get the patient if user is a patient.
     */
    public function patient(): HasOne
    {
        return $this->hasOne(Patient::class);
    }

    // ================= HELPER METHODS =================

    /**
     * Check if user has specific role type(s).
     */
    public function hasRoleType($roles): bool
    {
        if (is_string($roles)) {
            return $this->role_type === $roles;
        }

        if (is_array($roles)) {
            return in_array($this->role_type, $roles);
        }

        return false;
    }

    /**
     * Get or create user profile.
     */
    public function getOrCreateProfile(): UserProfile
    {
        return $this->profile ?: $this->profile()->create();
    }

    /**
     * Log user activity.
     */
    public function logActivity(
        string $action, 
        string $description, 
        ?Model $subject = null,
        array $properties = []
    ): ActivityLog {
        return ActivityLog::createLog($action, $description, $subject, $this, $properties);
    }

    /**
     * Get user's login activities.
     */
    public function getLoginActivities(int $limit = 10)
    {
        return $this->activityLogs()
                    ->whereIn('action', ['login', 'logout', 'login_failed'])
                    ->latest()
                    ->limit($limit)
                    ->get();
    }

    /**
     * Get user's certificate activities.
     */
    public function getCertificateActivities(int $limit = 10)
    {
        return $this->activityLogs()
                    ->certificates()
                    ->latest()
                    ->limit($limit)
                    ->get();
    }

    /**
     * Check if user has complete profile.
     */
    public function hasCompleteProfile(): bool
    {
        if (!$this->profile) {
            return false;
        }

        return $this->profile->getCompletionPercentage() >= 80;
    }

    /**
     * Check if user is a doctor with valid licenses.
     */
    public function isDoctorWithValidLicenses(): bool
    {
        return $this->role_type === 'doctor' && 
               $this->profile && 
               $this->profile->hasValidLicenses();
    }

    /**
     * Check if user has expiring licenses (within 30 days).
     */
    public function hasExpiringLicenses(): bool
    {
        return $this->profile && $this->profile->hasExpiringLicenses();
    }

    /**
     * Get user's notification preferences.
     */
    public function getNotificationPreferences(): array
    {
        return $this->profile?->notification_preferences ?? [];
    }

    /**
     * Update user's notification preference.
     */
    public function updateNotificationPreference(string $key, bool $value): void
    {
        $this->getOrCreateProfile()->updateNotificationPreference($key, $value);
    }

    /**
     * Get user's privacy settings.
     */
    public function getPrivacySettings(): array
    {
        return $this->profile?->privacy_settings ?? [];
    }

    /**
     * Update user's privacy setting.
     */
    public function updatePrivacySetting(string $key, $value): void
    {
        $this->getOrCreateProfile()->updatePrivacySetting($key, $value);
    }

    /**
     * Check if user allows email notifications.
     */
    public function allowsEmailNotifications(): bool
    {
        $preferences = $this->getNotificationPreferences();
        return $preferences['email_notifications'] ?? true;
    }

    /**
     * Check if user allows SMS notifications.
     */
    public function allowsSmsNotifications(): bool
    {
        $preferences = $this->getNotificationPreferences();
        return $preferences['sms_notifications'] ?? false;
    }

    /**
     * Get user's full name with credentials (for doctors).
     */
    public function getDisplayNameAttribute(): string
    {
        $displayName = $this->name;
        
        if ($this->role_type === 'doctor' && $this->profile) {
            $credentials = $this->profile->doctor_credentials;
            if ($credentials) {
                $displayName .= " ({$credentials})";
            }
        }
        
        return $displayName;
    }

    /**
     * Get user's role display name.
     */
    public function getRoleDisplayAttribute(): string
    {
        $roles = [
            'superadmin' => 'Super Administrator',
            'admin' => 'Administrator',
            'outlet' => 'Operator Outlet',
            'doctor' => 'Dokter',
            'companies' => 'Admin Perusahaan',
            'patient' => 'Pasien',
        ];

        return $roles[$this->role_type] ?? 'Unknown';
    }

    /**
     * Get user's status color for UI.
     */
    public function getStatusColorAttribute(): string
    {
        if ($this->is_banned) {
            return 'red';
        }
        
        if (!$this->is_active) {
            return 'gray';
        }
        
        if ($this->role_type === 'doctor' && !$this->isDoctorWithValidLicenses()) {
            return 'yellow';
        }
        
        return 'green';
    }

    /**
     * Get user's status text.
     */
    public function getStatusTextAttribute(): string
    {
        if ($this->is_banned) {
            return 'Diblokir';
        }
        
        if (!$this->is_active) {
            return 'Tidak Aktif';
        }
        
        if ($this->role_type === 'doctor' && !$this->isDoctorWithValidLicenses()) {
            return 'Lisensi Bermasalah';
        }
        
        return 'Aktif';
    }

    /**
     * Check if user is online (activity within last 15 minutes).
     */
    public function isOnline(): bool
    {
        return $this->last_activity_at && 
               $this->last_activity_at->diffInMinutes(now()) <= 15;
    }

    /**
     * Update user's last activity.
     */
    public function updateLastActivity(): void
    {
        $this->update([
            'last_activity_at' => now(),
            'last_ip' => request()->ip(),
        ]);
    }

    /**
     * Ban user with reason.
     */
    public function ban(string $reason = 'Pelanggaran kebijakan'): void
    {
        $this->update([
            'is_banned' => true,
            'banned_at' => now(),
            'ban_reason' => $reason,
            'is_active' => false,
        ]);

        // Log the ban
        $this->logActivity(
            'user_banned',
            "User {$this->name} was banned",
            $this,
            ['reason' => $reason, 'banned_by' => auth()->id()]
        );
    }

    /**
     * Unban user.
     */
    public function unban(): void
    {
        $this->update([
            'is_banned' => false,
            'banned_at' => null,
            'ban_reason' => null,
            'is_active' => true,
        ]);

        // Log the unban
        $this->logActivity(
            'user_unbanned',
            "User {$this->name} was unbanned",
            $this,
            ['unbanned_by' => auth()->id()]
        );
    }

    /**
     * Generate and set OTP code.
     */
    public function generateOtp(): string
    {
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        
        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10), // OTP valid for 10 minutes
        ]);

        return $otp;
    }

    /**
     * Verify OTP code.
     */
    public function verifyOtp(string $otp): bool
    {
        if (!$this->otp_code || !$this->otp_expires_at) {
            return false;
        }

        if (now()->isAfter($this->otp_expires_at)) {
            return false;
        }

        if ($this->otp_code !== $otp) {
            return false;
        }

        // Clear OTP after successful verification
        $this->update([
            'otp_code' => null,
            'otp_expires_at' => null,
        ]);

        return true;
    }

    /**
     * Clear expired OTP.
     */
    public function clearExpiredOtp(): void
    {
        if ($this->otp_expires_at && now()->isAfter($this->otp_expires_at)) {
            $this->update([
                'otp_code' => null,
                'otp_expires_at' => null,
            ]);
        }
    }

    // ================= SCOPES =================

    /**
     * Scope for active users.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->where('is_banned', false);
    }

    /**
     * Scope for banned users.
     */
    public function scopeBanned($query)
    {
        return $query->where('is_banned', true);
    }

    /**
     * Scope for users by role.
     */
    public function scopeByRole($query, $role)
    {
        return $query->where('role_type', $role);
    }

    /**
     * Scope for online users.
     */
    public function scopeOnline($query)
    {
        return $query->where('last_activity_at', '>=', now()->subMinutes(15));
    }

    /**
     * Scope for users with expiring licenses.
     */
    public function scopeWithExpiringLicenses($query)
    {
        return $query->whereHas('profile', function ($q) {
            $q->where(function ($subQuery) {
                $subQuery->where('str_expiry', '<=', now()->addDays(30))
                        ->orWhere('sip_expiry', '<=', now()->addDays(30));
            });
        });
    }

    // ================= BOOT METHODS =================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        // Auto-create profile for certain roles
        static::created(function ($user) {
            if (in_array($user->role_type, ['doctor', 'companies'])) {
                $user->profile()->create();
            }

            // Log user creation
            ActivityLog::createLog(
                'user_created',
                "New user created: {$user->name}",
                $user,
                auth()->user(),
                [
                    'email' => $user->email,
                    'role' => $user->role_type,
                ]
            );
        });

        // Log user updates
        static::updated(function ($user) {
            if ($user->wasChanged(['name', 'email', 'role_type', 'is_active', 'is_banned'])) {
                ActivityLog::createLog(
                    'user_updated',
                    "User updated: {$user->name}",
                    $user,
                    auth()->user(),
                    [
                        'changes' => $user->getChanges(),
                        'original' => $user->getOriginal(),
                    ]
                );
            }
        });

        // Log user deletion
        static::deleting(function ($user) {
            ActivityLog::createLog(
                'user_deleted',
                "User deleted: {$user->name}",
                $user,
                auth()->user(),
                [
                    'email' => $user->email,
                    'role' => $user->role_type,
                ]
            );
        });
    }
}