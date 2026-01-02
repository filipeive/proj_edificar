<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wedding extends Model
{
    protected $fillable = [
        'groom_name',
        'bride_name',
        'date',
        'time',
        'location',
        'godparents',
        'status',
        'observations',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime',
    ];
    //
}
