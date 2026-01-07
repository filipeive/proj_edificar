<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CellMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cell_id',
        'meeting_date',
        'theme',
        'biblical_text',
        'leader_id',
        'adults_count',
        'children_count',
        'visitors_count',
        'decisions',
        'meeting_type',
        'minutes',
        'observations',
    ];

    protected $casts = [
        'meeting_date' => 'date',
    ];

    public function cell()
    {
        return $this->belongsTo(Cell::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'cell_meeting_participants');
    }
}
