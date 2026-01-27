<?php

namespace App\Models;

use App\Models\Concerns\NormalizesMozPhone;
use Illuminate\Database\Eloquent\Model;

class CellVisitor extends Model
{
    use NormalizesMozPhone;
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

    public function setPhoneAttribute($value): void
    {
        $this->attributes['phone'] = $this->normalizeMozPhone($value);
    }

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }
}
