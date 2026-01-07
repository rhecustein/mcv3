<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class IcdMaster extends Model
{
    use HasFactory;

    protected $table = 'icd_masters';

    protected $fillable = [
        'code',
        'title',
        'description',
        'chapter',
        'version',
        'parent_code',
    ];

    /**
     * Relasi opsional: ICD child entries (jika memakai struktur ICD-11)
     */
    public function children()
    {
        return $this->hasMany(IcdMaster::class, 'parent_code', 'code');
    }

    /**
     * Relasi opsional: ICD parent entry
     */
    public function parent()
    {
        return $this->belongsTo(IcdMaster::class, 'parent_code', 'code');
    }

    /**
     * Medical diagnoses using this ICD code
     */
    public function diagnoses()
    {
        return $this->hasMany(MedicalDiagnosis::class, 'icd_master_id');
    }

    /**
     * Scope: Search by code or title
     */
    public function scopeSearch($query, string $keyword)
    {
        return $query->where('code', 'like', "%{$keyword}%")
            ->orWhere('title', 'like', "%{$keyword}%")
            ->orWhere('description', 'like', "%{$keyword}%");
    }

    /**
     * Scope: Filter by chapter
     */
    public function scopeChapter($query, string $chapter)
    {
        return $query->where('chapter', $chapter);
    }

    /**
     * Scope: Filter by version
     */
    public function scopeVersion($query, string $version)
    {
        return $query->where('version', $version);
    }
}
