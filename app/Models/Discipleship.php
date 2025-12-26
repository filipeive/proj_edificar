<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discipleship extends Model
{
    protected $fillable = [
        'cell_id',
        'user_id',
        'mentor_name',
        'start_date',
        'current_lesson',
    ];

    protected $casts = [
        'start_date' => 'date',
    ];

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
