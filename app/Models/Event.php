<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_type_id',
        'name',
        'date',
        'zone_id',
        'cell_id',
        'participants_count',
        'description',
        'observations',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function eventType()
    {
        return $this->belongsTo(EventType::class);
    }

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }
}
