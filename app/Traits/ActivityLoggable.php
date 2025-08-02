<?php

namespace App\Traits;

use App\Models\ActivityLog;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait ActivityLoggable
{
    /**
     * Get all activity logs for this model.
     */
    public function activityLogs(): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'model');
    }

    /**
     * Get recent activity logs.
     */
    public function recentActivityLogs(int $limit = 10): MorphMany
    {
        return $this->morphMany(ActivityLog::class, 'model')
                    ->latest()
                    ->limit($limit);
    }

    /**
     * Log activity for this model.
     */
    public function logActivity(
        string $action,
        string $description,
        ?User $user = null,
        array $properties = []
    ): ActivityLog {
        return ActivityLog::createLog($action, $description, $this, $user, $properties);
    }

    /**
     * Log creation activity.
     */
    public function logCreated(?User $user = null, array $properties = []): ActivityLog
    {
        $modelName = class_basename($this);
        
        return $this->logActivity(
            strtolower($modelName) . '_created',
            "{$modelName} created: {$this->getKey()}",
            $user,
            array_merge(['model_data' => $this->toArray()], $properties)
        );
    }

    /**
     * Log update activity.
     */
    public function logUpdated(?User $user = null, array $properties = []): ActivityLog
    {
        $modelName = class_basename($this);
        
        return $this->logActivity(
            strtolower($modelName) . '_updated',
            "{$modelName} updated: {$this->getKey()}",
            $user,
            array_merge([
                'changes' => $this->getChanges(),
                'original' => $this->getOriginal(),
            ], $properties)
        );
    }

    /**
     * Log deletion activity.
     */
    public function logDeleted(?User $user = null, array $properties = []): ActivityLog
    {
        $modelName = class_basename($this);
        
        return $this->logActivity(
            strtolower($modelName) . '_deleted',
            "{$modelName} deleted: {$this->getKey()}",
            $user,
            array_merge(['model_data' => $this->toArray()], $properties)
        );
    }

    /**
     * Boot the trait.
     */
    protected static function bootActivityLoggable()
    {
        // Auto-log model creation
        static::created(function ($model) {
            if (method_exists($model, 'shouldLogCreation') && !$model->shouldLogCreation()) {
                return;
            }
            
            $model->logCreated();
        });

        // Auto-log model updates
        static::updated(function ($model) {
            if (method_exists($model, 'shouldLogUpdate') && !$model->shouldLogUpdate()) {
                return;
            }
            
            $model->logUpdated();
        });

        // Auto-log model deletion
        static::deleting(function ($model) {
            if (method_exists($model, 'shouldLogDeletion') && !$model->shouldLogDeletion()) {
                return;
            }
            
            $model->logDeleted();
        });
    }

    /**
     * Determine if creation should be logged (override in model if needed).
     */
    public function shouldLogCreation(): bool
    {
        return true;
    }

    /**
     * Determine if updates should be logged (override in model if needed).
     */
    public function shouldLogUpdate(): bool
    {
        return true;
    }

    /**
     * Determine if deletion should be logged (override in model if needed).
     */
    public function shouldLogDeletion(): bool
    {
        return true;
    }
}