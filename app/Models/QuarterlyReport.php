<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterlyReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'supervision_id',
        'supervisor_id',
        'year',
        'quarter',
        'leaders_count',
        'cells_count',
        'timoteos_count',
        'members_count',
        'participants_count',
        'pastors_count',
        'supervisors_count',
        'visitors_count',
        'saved_count',
        'planned_baptism_count',
        'baptized_count',
        'cell_multiplications_count',
        'disciplined_leaders_count',
        'closed_cells_count',
        'ministerial_observations',
        'discipleship_score',
        'evangelism_strategy',
        'consolidation_growth',
        'pastoral_score',
        'visitation_routine',
        'leader_support',
        'cell_participation_score',
        'service_participation_score',
        'tadium_participation',
        'communion_in_cells_score',
        'relationship_building_score',
        'prayer_intercession_score',
        'submitted_at',
        'status',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }

    public function supervision()
    {
        return $this->belongsTo(Supervision::class);
    }

    public function supervisor()
    {
        return $this->belongsTo(User::class, 'supervisor_id');
    }

    public function events()
    {
        return $this->hasMany(QuarterlyReportEvent::class);
    }
}
