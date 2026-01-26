<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'user_id',
        'cell_id',
        'date',
        'type',
        'status',
        'reason',
    ];

    protected $casts = [
        'date' => 'date',
        'status' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function member()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }
}
