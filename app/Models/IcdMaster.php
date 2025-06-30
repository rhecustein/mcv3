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
}
