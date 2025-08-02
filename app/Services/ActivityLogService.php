<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ActivityLogService
{
    protected Request $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Log user authentication events.
     */
    public function logAuthentication(string $event, User $user, array $context = []): ActivityLog
    {
        $descriptions = [
            'login' => "User {$user->name} logged in successfully",
            'logout' => "User {$user->name} logged out",
            'login_failed' => "Failed login attempt for {$user->email}",
            'password_reset' => "Password reset requested for {$user->email}",
            'password_changed' => "Password changed for {$user->name}",
            'two_factor_enabled' => "Two-factor authentication enabled for {$user->name}",
            'two_factor_disabled' => "Two-factor authentication disabled for {$user->name}",
        ];

        return ActivityLog::createLog(
            $event,
            $descriptions[$event] ?? "Authentication event: {$event}",
            null,
            $user,
            array_merge([
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
                'timestamp' => now()->toISOString(),
            ], $context)
        );
    }

    /**
     * Log certificate-related activities.
     */
    public function logCertificate(string $action, Model $certificate, User $user, array $context = []): ActivityLog
    {
        $certificateType = $certificate->type ?? 'certificate';
        $patientName = $certificate->patient->user->name ?? 'Unknown';
        
        $descriptions = [
            'created' => "Created {$certificateType} for patient {$patientName}",
            'updated' => "Updated {$certificateType} for patient {$patientName}",
            'deleted' => "Deleted {$certificateType} for patient {$patientName}",
            'signed' => "Signed {$certificateType} for patient {$patientName}",
            'downloaded' => "Downloaded {$certificateType} for patient {$patientName}",
            'printed' => "Printed {$certificateType} for patient {$patientName}",
            'emailed' => "Emailed {$certificateType} for patient {$patientName}",
            'verified' => "Verified {$certificateType} for patient {$patientName}",
        ];

        return ActivityLog::createLog(
            "certificate_{$action}",
            $descriptions[$action] ?? "Certificate {$action}: {$certificate->getKey()}",
            $certificate,
            $user,
            array_merge([
                'certificate_id' => $certificate->getKey(),
                'certificate_type' => $certificateType,
                'patient_name' => $patientName,
            ], $context)
        );
    }

    /**
     * Log user management activities.
     */
    public function logUserManagement(string $action, User $targetUser, User $adminUser, array $context = []): ActivityLog
    {
        $descriptions = [
            'created' => "Admin {$adminUser->name} created user {$targetUser->name}",
            'updated' => "Admin {$adminUser->name} updated user {$targetUser->name}",
            'deleted' => "Admin {$adminUser->name} deleted user {$targetUser->name}",
            'banned' => "Admin {$adminUser->name} banned user {$targetUser->name}",
            'unbanned' => "Admin {$adminUser->name} unbanned user {$targetUser->name}",
            'role_changed' => "Admin {$adminUser->name} changed role for user {$targetUser->name}",
            'password_reset' => "Admin {$adminUser->name} reset password for user {$targetUser->name}",
        ];

        return ActivityLog::createLog(
            "user_{$action}",
            $descriptions[$action] ?? "User management: {$action}",
            $targetUser,
            $adminUser,
            array_merge([
                'target_user_id' => $targetUser->id,
                'target_user_email' => $targetUser->email,
                'admin_user_id' => $adminUser->id,
            ], $context)
        );
    }

    /**
     * Log security events.
     */
    public function logSecurity(string $event, ?User $user = null, array $context = []): ActivityLog
    {
        $descriptions = [
            'suspicious_login' => 'Suspicious login attempt detected',
            'multiple_failed_logins' => 'Multiple failed login attempts detected',
            'ip_blocked' => 'IP address blocked due to suspicious activity',
            'session_hijacking' => 'Potential session hijacking detected',
            'unauthorized_access' => 'Unauthorized access attempt',
            'data_export' => 'Data export performed',
            'system_backup' => 'System backup performed',
            'configuration_changed' => 'System configuration changed',
        ];

        return ActivityLog::createLog(
            "security_{$event}",
            $descriptions[$event] ?? "Security event: {$event}",
            null,
            $user,
            array_merge([
                'severity' => $this->getSecuritySeverity($event),
                'ip_address' => $this->request->ip(),
                'user_agent' => $this->request->userAgent(),
            ], $context)
        );
    }

    /**
     * Get security event severity.
     */
    protected function getSecuritySeverity(string $event): string
    {
        $highSeverity = ['session_hijacking', 'unauthorized_access', 'ip_blocked'];
        $mediumSeverity = ['suspicious_login', 'multiple_failed_logins'];
        
        if (in_array($event, $highSeverity)) {
            return 'high';
        }
        
        if (in_array($event, $mediumSeverity)) {
            return 'medium';
        }
        
        return 'low';
    }

    /**
     * Log system events.
     */
    public function logSystem(string $event, array $context = []): ActivityLog
    {
        $descriptions = [
            'maintenance_start' => 'System maintenance started',
            'maintenance_end' => 'System maintenance completed',
            'backup_created' => 'System backup created',
            'update_applied' => 'System update applied',
            'migration_run' => 'Database migration executed',
            'cache_cleared' => 'System cache cleared',
            'queue_processed' => 'Queue jobs processed',
        ];

        return ActivityLog::createLog(
            "system_{$event}",
            $descriptions[$event] ?? "System event: {$event}",
            null,
            auth()->user(),
            array_merge([
                'system_event' => true,
                'timestamp' => now()->toISOString(),
            ], $context)
        );
    }

    /**
     * Get activity statistics for dashboard.
     */
    public function getStatistics(int $days = 30): array
    {
        $cacheKey = "activity_stats_{$days}_days_" . now()->format('Y-m-d');
        
        return Cache::remember($cacheKey, 3600, function () use ($days) {
            $startDate = now()->subDays($days);
            
            $baseQuery = ActivityLog::where('created_at', '>=', $startDate);
            
            return [
                'total_activities' => $baseQuery->count(),
                'unique_users' => $baseQuery->whereNotNull('user_id')->distinct('user_id')->count(),
                'authentication_events' => $baseQuery->whereIn('action', [
                    'login', 'logout', 'login_failed', 'password_changed'
                ])->count(),
                'certificate_activities' => $baseQuery->where('action', 'like', 'certificate_%')->count(),
                'security_events' => $baseQuery->where('action', 'like', 'security_%')->count(),
                'user_management' => $baseQuery->where('action', 'like', 'user_%')->count(),
                'system_events' => $baseQuery->where('action', 'like', 'system_%')->count(),
                'daily_breakdown' => $this->getDailyBreakdown($days),
                'top_actions' => $this->getTopActions($days),
                'most_active_users' => $this->getMostActiveUsers($days),
            ];
        });
    }

    /**
     * Get daily activity breakdown.
     */
    protected function getDailyBreakdown(int $days): array
    {
        return ActivityLog::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn($item) => [$item->date => $item->count])
            ->toArray();
    }

    /**
     * Get top actions.
     */
    protected function getTopActions(int $days): array
    {
        return ActivityLog::selectRaw('action, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('action')
            ->orderByDesc('count')
            ->limit(10)
            ->get()
            ->mapWithKeys(fn($item) => [$item->action => $item->count])
            ->toArray();
    }

    /**
     * Get most active users.
     */
    protected function getMostActiveUsers(int $days): array
    {
        return ActivityLog::with('user:id,name,email')
            ->selectRaw('user_id, COUNT(*) as activity_count')
            ->where('created_at', '>=', now()->subDays($days))
            ->whereNotNull('user_id')
            ->groupBy('user_id')
            ->orderByDesc('activity_count')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'user' => $log->user,
                    'activity_count' => $log->activity_count,
                ];
            })
            ->toArray();
    }

    /**
     * Clean up old activity logs.
     */
    public function cleanup(int $keepDays = 365): int
    {
        $cutoffDate = now()->subDays($keepDays);
        
        return ActivityLog::where('created_at', '<', $cutoffDate)->delete();
    }

    /**
     * Export activity logs to CSV.
     */
    public function exportToCsv(array $filters = []): string
    {
        $query = ActivityLog::with('user:id,name,email');
        
        // Apply filters
        if (isset($filters['start_date'])) {
            $query->where('created_at', '>=', $filters['start_date']);
        }
        
        if (isset($filters['end_date'])) {
            $query->where('created_at', '<=', $filters['end_date']);
        }
        
        if (isset($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }
        
        if (isset($filters['action'])) {
            $query->where('action', $filters['action']);
        }
        
        $logs = $query->orderBy('created_at', 'desc')->get();
        
        $filename = 'activity_logs_' . now()->format('Y-m-d_H-i-s') . '.csv';
        $filepath = storage_path('app/exports/' . $filename);
        
        // Ensure directory exists
        if (!file_exists(dirname($filepath))) {
            mkdir(dirname($filepath), 0755, true);
        }
        
        $file = fopen($filepath, 'w');
        
        // Headers
        fputcsv($file, [
            'Date',
            'User',
            'Email',
            'Action',
            'Description',
            'IP Address',
            'User Agent'
        ]);
        
        // Data
        foreach ($logs as $log) {
            fputcsv($file, [
                $log->created_at->format('Y-m-d H:i:s'),
                $log->user?->name ?? 'System',
                $log->user?->email ?? '',
                $log->formatted_action,
                $log->description,
                $log->ip_address ?? '',
                $log->user_agent ?? ''
            ]);
        }
        
        fclose($file);
        
        return $filepath;
    }
}