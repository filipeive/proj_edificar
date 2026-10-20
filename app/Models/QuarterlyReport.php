<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuarterlyReport extends CongregationModel
{
    use HasFactory;

    protected $fillable = [
        'zone_id',
        'supervision_id',
        'supervisor_id',
        'zone_pastor_id',
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

    public function zonePastor()
    {
        return $this->belongsTo(User::class, 'zone_pastor_id');
    }

    public function events()
    {
        return $this->hasMany(QuarterlyReportEvent::class);
    }

    public static function healthMetricsMap(): array
    {
        return [
            'discipleship_score' => 'Discipulado',
            'evangelism_strategy' => 'Estratégia de Evangelismo',
            'consolidation_growth' => 'Consolidação e Crescimento',
            'pastoral_score' => 'Cuidado Pastoral',
            'visitation_routine' => 'Rotina de Visitação',
            'leader_support' => 'Apoio aos Líderes',
            'cell_participation_score' => 'Participação nas Células',
            'service_participation_score' => 'Participação nos Cultos',
            'tadium_participation' => 'Participação no TADEL',
            'communion_in_cells_score' => 'Comunhão nas Células',
            'relationship_building_score' => 'Construção de Relacionamentos',
            'prayer_intercession_score' => 'Oração e Intercessão',
        ];
    }

    public function getHealthTotalScoreAttribute(): int
    {
        $total = 0;
        foreach (array_keys(self::healthMetricsMap()) as $field) {
            $total += (int) ($this->{$field} ?? 0);
        }

        return $total;
    }

    public function getHealthAverageAttribute(): float
    {
        return round($this->health_total_score / 12, 1);
    }

    public function getHealthPercentageAttribute(): int
    {
        return (int) round(($this->health_total_score / 36) * 100);
    }
}
