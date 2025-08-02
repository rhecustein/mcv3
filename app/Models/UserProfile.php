<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class UserProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'user_id',
        // Personal Information
        'nik',
        'passport_number',
        'passport_expiry',
        'emergency_contact_name',
        'emergency_contact_phone',
        
        // Professional Information (for doctors)
        'str_number',
        'str_expiry',
        'sip_number',
        'sip_expiry',
        'specialization',
        'qualifications',
        
        // Company Information (for company users)
        'company_name',
        'company_position',
        'employee_id',
        
        // Preferences (JSON fields)
        'notification_preferences',
        'privacy_settings',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'passport_expiry' => 'date',
        'str_expiry' => 'date',
        'sip_expiry' => 'date',
        'notification_preferences' => 'array',
        'privacy_settings' => 'array',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'nik', // Sensitive personal data
    ];

    // ================= RELATIONSHIPS =================

    /**
     * Get the user that owns the profile.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ================= SCOPES =================

    /**
     * Scope for doctors only.
     */
    public function scopeDoctors($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('role_type', 'doctor');
        });
    }

    /**
     * Scope for company users only.
     */
    public function scopeCompanyUsers($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('role_type', 'companies');
        });
    }

    /**
     * Scope for profiles with valid STR (doctor license).
     */
    public function scopeValidStr($query)
    {
        return $query->whereNotNull('str_number')
                    ->where('str_expiry', '>', now());
    }

    /**
     * Scope for profiles with valid SIP (practice license).
     */
    public function scopeValidSip($query)
    {
        return $query->whereNotNull('sip_number')
                    ->where('sip_expiry', '>', now());
    }

    // ================= ACCESSORS & MUTATORS =================

    /**
     * Get the masked NIK for display.
     */
    public function getMaskedNikAttribute(): ?string
    {
        if (!$this->nik) {
            return null;
        }
        
        return substr($this->nik, 0, 4) . '****' . substr($this->nik, -4);
    }

    /**
     * Get full doctor credentials.
     */
    public function getDoctorCredentialsAttribute(): string
    {
        $credentials = [];
        
        if ($this->str_number) {
            $credentials[] = "STR: {$this->str_number}";
        }
        
        if ($this->sip_number) {
            $credentials[] = "SIP: {$this->sip_number}";
        }
        
        if ($this->specialization) {
            $credentials[] = "Sp. {$this->specialization}";
        }
        
        return implode(' | ', $credentials);
    }

    /**
     * Get full company info.
     */
    public function getCompanyInfoAttribute(): ?string
    {
        if (!$this->company_name) {
            return null;
        }
        
        $info = $this->company_name;
        
        if ($this->company_position) {
            $info .= " - {$this->company_position}";
        }
        
        if ($this->employee_id) {
            $info .= " (ID: {$this->employee_id})";
        }
        
        return $info;
    }

    /**
     * Get notification preferences with defaults.
     */
    public function getNotificationPreferencesAttribute($value): array
    {
        $defaults = [
            'email_notifications' => true,
            'sms_notifications' => false,
            'push_notifications' => true,
            'marketing_emails' => false,
            'certificate_reminders' => true,
            'license_expiry_alerts' => true,
        ];
        
        return array_merge($defaults, json_decode($value, true) ?? []);
    }

    /**
     * Get privacy settings with defaults.
     */
    public function getPrivacySettingsAttribute($value): array
    {
        $defaults = [
            'profile_visibility' => 'private',
            'show_email' => false,
            'show_phone' => false,
            'allow_contact' => true,
            'data_sharing' => false,
        ];
        
        return array_merge($defaults, json_decode($value, true) ?? []);
    }

    // ================= HELPER METHODS =================

    /**
     * Check if doctor has valid licenses.
     */
    public function hasValidLicenses(): bool
    {
        return $this->hasValidStr() && $this->hasValidSip();
    }

    /**
     * Check if STR is valid and not expired.
     */
    public function hasValidStr(): bool
    {
        return $this->str_number && 
               $this->str_expiry && 
               $this->str_expiry->isFuture();
    }

    /**
     * Check if SIP is valid and not expired.
     */
    public function hasValidSip(): bool
    {
        return $this->sip_number && 
               $this->sip_expiry && 
               $this->sip_expiry->isFuture();
    }

    /**
     * Check if passport is valid and not expired.
     */
    public function hasValidPassport(): bool
    {
        return $this->passport_number && 
               $this->passport_expiry && 
               $this->passport_expiry->isFuture();
    }

    /**
     * Get days until STR expiry.
     */
    public function getDaysUntilStrExpiry(): ?int
    {
        if (!$this->str_expiry) {
            return null;
        }
        
        return now()->diffInDays($this->str_expiry, false);
    }

    /**
     * Get days until SIP expiry.
     */
    public function getDaysUntilSipExpiry(): ?int
    {
        if (!$this->sip_expiry) {
            return null;
        }
        
        return now()->diffInDays($this->sip_expiry, false);
    }

    /**
     * Check if licenses are expiring soon (within 30 days).
     */
    public function hasExpiringLicenses(): bool
    {
        $strExpiring = $this->str_expiry && 
                      $this->str_expiry->isFuture() && 
                      $this->str_expiry->diffInDays(now()) <= 30;
                      
        $sipExpiring = $this->sip_expiry && 
                      $this->sip_expiry->isFuture() && 
                      $this->sip_expiry->diffInDays(now()) <= 30;
        
        return $strExpiring || $sipExpiring;
    }

    /**
     * Update notification preference.
     */
    public function updateNotificationPreference(string $key, bool $value): void
    {
        $preferences = $this->notification_preferences;
        $preferences[$key] = $value;
        $this->update(['notification_preferences' => $preferences]);
    }

    /**
     * Update privacy setting.
     */
    public function updatePrivacySetting(string $key, $value): void
    {
        $settings = $this->privacy_settings;
        $settings[$key] = $value;
        $this->update(['privacy_settings' => $settings]);
    }

    /**
     * Get profile completion percentage.
     */
    public function getCompletionPercentage(): int
    {
        $fields = [
            'nik',
            'emergency_contact_name',
            'emergency_contact_phone',
        ];
        
        // Add role-specific fields
        if ($this->user && $this->user->role_type === 'doctor') {
            $fields = array_merge($fields, [
                'str_number',
                'str_expiry', 
                'sip_number',
                'sip_expiry',
                'specialization'
            ]);
        }
        
        if ($this->user && $this->user->role_type === 'companies') {
            $fields = array_merge($fields, [
                'company_name',
                'company_position',
                'employee_id'
            ]);
        }
        
        $filled = 0;
        foreach ($fields as $field) {
            if (!empty($this->$field)) {
                $filled++;
            }
        }
        
        return round(($filled / count($fields)) * 100);
    }

    // ================= BOOT METHODS =================

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();
        
        // Auto-set default preferences on creation
        static::creating(function ($profile) {
            if (empty($profile->notification_preferences)) {
                $profile->notification_preferences = [
                    'email_notifications' => true,
                    'certificate_reminders' => true,
                    'license_expiry_alerts' => true,
                ];
            }
            
            if (empty($profile->privacy_settings)) {
                $profile->privacy_settings = [
                    'profile_visibility' => 'private',
                    'allow_contact' => true,
                ];
            }
        });
    }
}