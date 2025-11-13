<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PlatformAdmin extends Model
{
    protected $fillable = [
        'user_id',
        'role',
        'permissions',
        'is_active',
        'last_login_at',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    /**
     * Relationship to User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Check if admin has specific permission
     */
    public function hasPermission(string $permission): bool
    {
        if ($this->role === 'super_admin') {
            return true; // Super admin has all permissions
        }

        $permissions = $this->permissions ?? [];
        return in_array($permission, $permissions);
    }

    /**
     * Check if admin can manage tenants
     */
    public function canManageTenants(): bool
    {
        return $this->hasPermission('manage_tenants') || $this->role === 'super_admin';
    }

    /**
     * Check if admin can manage billing
     */
    public function canManageBilling(): bool
    {
        return $this->hasPermission('manage_billing') ||
               in_array($this->role, ['super_admin', 'billing']);
    }

    /**
     * Check if admin can view analytics
     */
    public function canViewAnalytics(): bool
    {
        return $this->hasPermission('view_analytics') ||
               in_array($this->role, ['super_admin', 'admin']);
    }

    /**
     * Scope for active admins
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope by role
     */
    public function scopeByRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    /**
     * Get all available permissions
     */
    public static function getAvailablePermissions(): array
    {
        return [
            'manage_tenants' => 'Manage Tenants (CRUD)',
            'manage_billing' => 'Manage Billing & Invoices',
            'view_analytics' => 'View System Analytics',
            'manage_users' => 'Manage Platform Users',
            'manage_subscriptions' => 'Manage Subscription Plans',
            'send_notifications' => 'Send System Notifications',
            'view_logs' => 'View System Logs',
            'manage_settings' => 'Manage Platform Settings',
        ];
    }

    /**
     * Get role display name
     */
    public function getRoleDisplayAttribute(): string
    {
        return match($this->role) {
            'super_admin' => 'Super Administrator',
            'admin' => 'Administrator',
            'billing' => 'Billing Manager',
            'support' => 'Support Staff',
            default => 'Unknown',
        };
    }

    /**
     * Update last login
     */
    public function updateLastLogin(): void
    {
        $this->update(['last_login_at' => now()]);
    }
}
