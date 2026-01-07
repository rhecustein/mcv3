<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TemplateResult extends Model
{
    use HasFactory;

    /**
     * Mass assignable attributes
     */
    protected $fillable = [
        'name',
        'type',
        'content',
        'header',
        'footer',
        'is_active',
        'outlet_id',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Outlet that owns this template
     */
    public function outlet()
    {
        return $this->belongsTo(Outlet::class);
    }

    /**
     * Results using this template
     */
    public function results()
    {
        return $this->hasMany(Result::class, 'template_result_id');
    }

    /**
     * Scope: Only active templates
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Filter by type
     */
    public function scopeType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
