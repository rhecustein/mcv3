<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ResultTrashView extends Model
{
    protected $table = 'result_trash_view';

    protected $fillable = [
        'id', 'deleted_at', 'deleted_ip', 'deleted_location',
        'deleted_by', 'deleted_outlet_id',
    ];

    public $timestamps = false;

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    public function outlet()
    {
        return $this->belongsTo(Outlet::class, 'deleted_outlet_id');
    }
}
