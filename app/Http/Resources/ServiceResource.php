<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
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
            'date' => $this->date?->toDateString(),
            'service_type' => $this->service_type,
            'preacher_id' => $this->preacher_id,
            'preacher_name' => $this->preacher_name,
            'theme' => $this->theme,
            'message' => $this->message,
            'observations' => $this->observations,
            'adults_members' => $this->service_type === 'teaching'
                ? (int) $this->zoneParticipations->sum(function ($p) {
                    return $p->adults_members + $p->leaders + $p->auxiliary_leaders + $p->supervisors + $p->zone_pastors;
                })
                : (int) $this->adults_members,
            'adults_visitors' => $this->service_type === 'teaching'
                ? (int) ($this->zoneParticipations->sum('adults_visitors') + ($this->adults_visitors ?? 0))
                : (int) $this->adults_visitors,
            'adults_salvations' => (int) $this->adults_salvations,
            'children_members' => $this->service_type === 'teaching'
                ? (int) $this->zoneParticipations->sum('children_members')
                : (int) $this->children_members,
            'children_visitors' => $this->service_type === 'teaching'
                ? (int) ($this->zoneParticipations->sum('children_visitors') + ($this->children_visitors ?? 0))
                : (int) $this->children_visitors,
            'children_salvations' => (int) $this->children_salvations,
            'special_offerings_total' => (float) $this->special_offerings_total,
            'total_offerings' => (float) $this->total_offerings,
            'total_participation' => (int) $this->total_participation,
            'created_at' => $this->created_at?->toIso8601String(),
            'preacher' => new UserResource($this->whenLoaded('preacher')),
            'zone_participations' => $this->when($this->service_type === 'teaching' && $this->relationLoaded('zoneParticipations'), function () {
                return $this->zoneParticipations->map(fn($p) => [
                    'id' => $p->id,
                    'zone_id' => $p->zone_id,
                    'zone_name' => $p->zone->name ?? null,
                    'adults_members' => (int) $p->adults_members,
                    'adults_visitors' => (int) $p->adults_visitors,
                    'leaders' => (int) $p->leaders,
                    'supervisors' => (int) $p->supervisors,
                    'auxiliary_leaders' => (int) $p->auxiliary_leaders,
                    'zone_pastors' => (int) $p->zone_pastors,
                    'children_members' => (int) $p->children_members,
                    'children_visitors' => (int) $p->children_visitors,
                    'total' => (int) $p->total,
                ]);
            }),
        ];
    }
}
