<?php

namespace App\Models\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class TenantScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     *
     * Automatically filters all queries by the current tenant_id.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Only apply if we have a tenant in the container
        if (app()->bound('tenant')) {
            $tenant = app('tenant');

            if ($tenant && $tenant->id) {
                $builder->where($model->getTable() . '.tenant_id', $tenant->id);
            }
        }
    }

    /**
     * Extend the query builder with useful tenant-related methods.
     */
    public function extend(Builder $builder): void
    {
        // Add withoutTenant() method to bypass the scope
        $builder->macro('withoutTenant', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });

        // Add forTenant() method to manually specify tenant
        $builder->macro('forTenant', function (Builder $builder, $tenantId) {
            return $builder->withoutGlobalScope($this)
                ->where($builder->getModel()->getTable() . '.tenant_id', $tenantId);
        });

        // Add allTenants() method to query across all tenants (admin only)
        $builder->macro('allTenants', function (Builder $builder) {
            return $builder->withoutGlobalScope($this);
        });
    }
}
