<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CellVisitor extends Model
{
    protected $fillable = [
        'cell_id',
        'name',
        'phone',
        'address',
        'visit_date',
        'observations',
        'became_participant',
    ];

    protected $casts = [
        'visit_date' => 'date',
        'became_participant' => 'boolean',
    ];

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }
}
