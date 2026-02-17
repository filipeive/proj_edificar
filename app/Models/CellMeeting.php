<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CellMeeting extends Model
{
    use HasFactory;

    protected $fillable = [
        'cell_id',
        'zone_id',
        'supervision_id',
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

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function supervision()
    {
        return $this->belongsTo(Supervision::class);
    }

    public function leader()
    {
        return $this->belongsTo(User::class, 'leader_id');
    }

    public function participants()
    {
        return $this->belongsToMany(User::class, 'cell_meeting_participants');
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'cell_id', 'cell_id')
            ->where('type', 'cell');
    }

    public function visitors()
    {
        return $this->hasMany(Visitor::class, 'cell_id', 'cell_id');
    }

    /**
     * Check if this is a cell-specific meeting (normal type).
     */
    public function isCellMeeting(): bool
    {
        return $this->meeting_type === 'normal';
    }

    /**
     * Get a human-readable label for the meeting type.
     */
    public function getMeetingTypeLabelAttribute(): string
    {
        return match ($this->meeting_type) {
            'leadership' => 'Reunião de Liderança',
            'supervision' => 'Reunião de Supervisão',
            'zone' => 'Reunião de Zona',
            'general' => 'Reunião Geral',
            'other' => 'Encontro Especial',
            default => 'Reunião de Célula',
        };
    }
}
