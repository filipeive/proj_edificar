<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuarterlyReportResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'zone_id' => $this->zone_id,
            'supervision_id' => $this->supervision_id,
            'supervisor_id' => $this->supervisor_id,
            'zone_pastor_id' => $this->zone_pastor_id,
            'year' => (int) $this->year,
            'quarter' => (int) $this->quarter,
            'leaders_count' => (int) $this->leaders_count,
            'cells_count' => (int) $this->cells_count,
            'timoteos_count' => (int) $this->timoteos_count,
            'members_count' => (int) $this->members_count,
            'participants_count' => (int) $this->participants_count,
            'pastors_count' => (int) $this->pastors_count,
            'supervisors_count' => (int) $this->supervisors_count,
            'visitors_count' => (int) $this->visitors_count,
            'saved_count' => (int) $this->saved_count,
            'planned_baptism_count' => (int) $this->planned_baptism_count,
            'baptized_count' => (int) $this->baptized_count,
            'cell_multiplications_count' => (int) $this->cell_multiplications_count,
            'disciplined_leaders_count' => (int) $this->disciplined_leaders_count,
            'closed_cells_count' => (int) $this->closed_cells_count,
            'ministerial_observations' => $this->ministerial_observations,
            'discipleship_score' => (int) $this->discipleship_score,
            'evangelism_strategy' => $this->evangelism_strategy,
            'consolidation_growth' => $this->consolidation_growth,
            'pastoral_score' => (int) $this->pastoral_score,
            'visitation_routine' => $this->visitation_routine,
            'leader_support' => $this->leader_support,
            'cell_participation_score' => (int) $this->cell_participation_score,
            'service_participation_score' => (int) $this->service_participation_score,
            'tadium_participation' => $this->tadium_participation,
            'communion_in_cells_score' => (int) $this->communion_in_cells_score,
            'relationship_building_score' => (int) $this->relationship_building_score,
            'prayer_intercession_score' => (int) $this->prayer_intercession_score,
            'status' => $this->status,
            'submitted_at' => $this->submitted_at?->toIso8601String(),
            'created_at' => $this->created_at?->toIso8601String(),
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'supervision' => new SupervisionResource($this->whenLoaded('supervision')),
            'supervisor' => new UserResource($this->whenLoaded('supervisor')),
        ];
    }
}
