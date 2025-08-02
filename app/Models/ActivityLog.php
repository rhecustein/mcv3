<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class ActivityLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        'action',
        'model_type',
        'model_id', 
        'description',
        'properties',
        'ip_address',
        'user_agent',
        'session_id',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'properties' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ================= RELATIONSHIPS =================

    /**
     * Get the user that performed the activity.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the model that the activity was performed on.
     */
    public function subject(): MorphTo
    {
        return $this->morphTo('model');
    }

    // ================= SCOPES =================

    /**
     * Scope for activities by specific user.
     */
    public function scopeByUser(Builder $query, $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope for activities by action type.
     */
    public function scopeByAction(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Scope for activities in date range.
     */
    public function scopeDateRange(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    /**
     * Scope for activities today.
     */
    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('created_at', today());
    }

    /**
     * Scope for activities this week.
     */
    public function scopeThisWeek(Builder $query): Builder
    {
        return $query->whereBetween('created_at', [
            now()->startOfWeek(), 
            now()->endOfWeek()
        ]);
    }

    /**
     * Scope for activities this month.
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year);
    }

    /**
     * Scope for login activities.
     */
    public function scopeLogins(Builder $query): Builder
    {
        return $query->whereIn('action', ['login', 'logout']);
    }

    /**
     * Scope for certificate activities.
     */
    public function scopeCertificates(Builder $query): Builder
    {
        return $query->whereIn('action', [
            'certificate_created',
            'certificate_updated', 
            'certificate_deleted',
            'certificate_signed',
            'certificate_downloaded'
        ]);
    }

    /**
     * Scope for security-related activities.
     */
    public function scopeSecurity(Builder $query): Builder
    {
        return $query->whereIn('action', [
            'login',
            'login_failed',
            'logout',
            'password_changed',
            'password_reset',
            'two_factor_enabled',
            'two_factor_disabled',
            'account_banned',
            'account_unbanned'
        ]);
    }

    /**
     * Scope for administrative activities.
     */
    public function scopeAdministrative(Builder $query): Builder
    {
        return $query->whereIn('action', [
            'user_created',
            'user_updated',
            'user_deleted',
            'user_banned',
            'user_unbanned',
            'role_changed',
            'permissions_updated'
        ]);
    }

    // ================= ACCESSORS =================

    /**
     * Get formatted action for display.
     */
    public function getFormattedActionAttribute(): string
    {
        $actions = [
            'login' => 'Masuk ke sistem',
            'logout' => 'Keluar dari sistem',
            'login_failed' => 'Gagal masuk',
            'certificate_created' => 'Membuat surat',
            'certificate_updated' => 'Mengubah surat',
            'certificate_deleted' => 'Menghapus surat',
            'certificate_signed' => 'Menandatangani surat',
            'certificate_downloaded' => 'Mengunduh surat',
            'user_created' => 'Membuat pengguna',
            'user_updated' => 'Mengubah pengguna',
            'user_deleted' => 'Menghapus pengguna',
            'user_banned' => 'Memblokir pengguna',
            'user_unbanned' => 'Membuka blokir pengguna',
            'password_changed' => 'Mengubah kata sandi',
            'password_reset' => 'Reset kata sandi',
            'profile_updated' => 'Mengubah profil',
        ];

        return $actions[$this->action] ?? ucwords(str_replace('_', ' ', $this->action));
    }

    /**
     * Get activity icon based on action.
     */
    public function getIconAttribute(): string
    {
        $icons = [
            'login' => 'login',
            'logout' => 'logout',
            'login_failed' => 'shield-exclamation',
            'certificate_created' => 'document-plus',
            'certificate_updated' => 'document-text',
            'certificate_deleted' => 'document-minus',
            'certificate_signed' => 'pencil',
            'certificate_downloaded' => 'arrow-down-tray',
            'user_created' => 'user-plus',
            'user_updated' => 'user',
            'user_deleted' => 'user-minus',
            'user_banned' => 'no-symbol',
            'user_unbanned' => 'check-circle',
            'password_changed' => 'key',
            'password_reset' => 'arrow-path',
            'profile_updated' => 'user-circle',
        ];

        return $icons[$this->action] ?? 'information-circle';
    }

    /**
     * Get activity color based on action type.
     */
    public function getColorAttribute(): string
    {
        $securityActions = ['login', 'logout', 'password_changed', 'password_reset'];
        $certificateActions = ['certificate_created', 'certificate_updated', 'certificate_signed'];
        $dangerActions = ['certificate_deleted', 'user_deleted', 'user_banned', 'login_failed'];
        $successActions = ['certificate_downloaded', 'user_created', 'user_unbanned'];

        if (in_array($this->action, $securityActions)) {
            return 'blue';
        } elseif (in_array($this->action, $certificateActions)) {
            return 'green';
        } elseif (in_array($this->action, $dangerActions)) {
            return 'red';
        } elseif (in_array($this->action, $successActions)) {
            return 'emerald';
        }

        return 'gray';
    }

    /**
     * Get human-readable time difference.
     */
    public function getTimeAgoAttribute(): string
    {
        return $this->created_at->diffForHumans();
    }

    /**
     * Get browser info from user agent.
     */
    public function getBrowserInfoAttribute(): array
    {
        if (!$this->user_agent) {
            return ['browser' => 'Unknown', 'platform' => 'Unknown'];
        }

        $agent = $this->user_agent;
        
        // Simple browser detection
        if (strpos($agent, 'Chrome') !== false) {
            $browser = 'Chrome';
        } elseif (strpos($agent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($agent, 'Safari') !== false) {
            $browser = 'Safari';
        } elseif (strpos($agent, 'Edge') !== false) {
            $browser = 'Edge';
        } else {
            $browser = 'Unknown';
        }

        // Simple platform detection
        if (strpos($agent, 'Windows') !== false) {
            $platform = 'Windows';
        } elseif (strpos($agent, 'Mac') !== false) {
            $platform = 'macOS';
        } elseif (strpos($agent, 'Linux') !== false) {
            $platform = 'Linux';
        } elseif (strpos($agent, 'Android') !== false) {
            $platform = 'Android';
        } elseif (strpos($agent, 'iPhone') !== false || strpos($agent, 'iPad') !== false) {
            $platform = 'iOS';
        } else {
            $platform = 'Unknown';
        }

        return ['browser' => $browser, 'platform' => $platform];
    }

    // ================= STATIC METHODS =================

    /**
     * Create activity log entry.
     */
    public static function createLog(
        string $action,
        string $description,
        ?Model $subject = null,
        ?User $user = null,
        array $properties = []
    ): self {
        $user = $user ?? auth()->user();
        
        return self::create([
            'user_id' => $user?->id,
            'action' => $action,
            'model_type' => $subject ? get_class($subject) : null,
            'model_id' => $subject?->getKey(),
            'description' => $description,
            'properties' => $properties,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => session()->getId(),
        ]);
    }

    /**
     * Log user login.
     */
    public static function logLogin(User $user, array $properties = []): self
    {
        return self::createLog(
            'login',
            "User {$user->name} logged in",
            null,
            $user,
            array_merge([
                'email' => $user->email,
                'role' => $user->role_type,
                'login_at' => now()->toISOString(),
            ], $properties)
        );
    }

    /**
     * Log user logout.
     */
    public static function logLogout(User $user, array $properties = []): self
    {
        return self::createLog(
            'logout',
            "User {$user->name} logged out",
            null,
            $user,
            array_merge([
                'email' => $user->email,
                'logout_at' => now()->toISOString(),
            ], $properties)
        );
    }

    /**
     * Log failed login attempt.
     */
    public static function logFailedLogin(string $email, array $properties = []): self
    {
        return self::createLog(
            'login_failed',
            "Failed login attempt for {$email}",
            null,
            null,
            array_merge([
                'email' => $email,
                'attempted_at' => now()->toISOString(),
            ], $properties)
        );
    }

    /**
     * Log certificate creation.
     */
    public static function logCertificateCreated(Model $certificate, User $user, array $properties = []): self
    {
        return self::createLog(
            'certificate_created',
            "Certificate created: {$certificate->getKey()}",
            $certificate,
            $user,
            $properties
        );
    }

    /**
     * Log certificate update.
     */
    public static function logCertificateUpdated(Model $certificate, User $user, array $properties = []): self
    {
        return self::createLog(
            'certificate_updated',
            "Certificate updated: {$certificate->getKey()}",
            $certificate,
            $user,
            $properties
        );
    }

    /**
     * Log certificate deletion.
     */
    public static function logCertificateDeleted(Model $certificate, User $user, array $properties = []): self
    {
        return self::createLog(
            'certificate_deleted',
            "Certificate deleted: {$certificate->getKey()}",
            $certificate,
            $user,
            $properties
        );
    }

    /**
     * Get activity statistics for dashboard.
     */
    public static function getStatistics(int $days = 30): array
    {
        $startDate = now()->subDays($days);
        
        return [
            'total_activities' => self::where('created_at', '>=', $startDate)->count(),
            'unique_users' => self::where('created_at', '>=', $startDate)->distinct('user_id')->count(),
            'login_attempts' => self::where('created_at', '>=', $startDate)
                                   ->whereIn('action', ['login', 'login_failed'])
                                   ->count(),
            'certificates_created' => self::where('created_at', '>=', $startDate)
                                         ->where('action', 'certificate_created')
                                         ->count(),
            'most_active_users' => self::where('created_at', '>=', $startDate)
                                      ->with('user:id,name,email')
                                      ->selectRaw('user_id, count(*) as activity_count')
                                      ->groupBy('user_id')
                                      ->orderByDesc('activity_count')
                                      ->limit(10)
                                      ->get(),
        ];
    }

    // ================= BOOT METHODS =================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-cleanup old logs (keep only 1 year)
        static::created(function () {
            if (rand(1, 100) === 1) { // 1% chance to cleanup
                self::where('created_at', '<', now()->subYear())->delete();
            }
        });
    }
}