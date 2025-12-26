<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversion extends Model
{
    protected $fillable = [
        'cell_id',
        'name',
        'date',
        'is_new_salvation',
        'is_water_baptism_candidate',
        'is_holy_spirit_baptism_candidate',
    ];

    protected $casts = [
        'date' => 'date',
        'is_new_salvation' => 'boolean',
        'is_water_baptism_candidate' => 'boolean',
        'is_holy_spirit_baptism_candidate' => 'boolean',
    ];

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }
}
