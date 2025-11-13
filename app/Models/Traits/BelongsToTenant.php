<?php

namespace App\Models\Traits;

use App\Models\Scopes\TenantScope;
use App\Models\Tenant;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTenant
{
    /**
     * Boot the trait and apply the TenantScope.
     */
    protected static function bootBelongsToTenant(): void
    {
        static::addGlobalScope(new TenantScope());

        // Automatically set tenant_id on creation
        static::creating(function ($model) {
            if (app()->bound('tenant') && !$model->tenant_id) {
                $tenant = app('tenant');
                if ($tenant && $tenant->id) {
                    $model->tenant_id = $tenant->id;
                }
            }
        });
    }

    /**
     * Relationship to tenant.
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}
