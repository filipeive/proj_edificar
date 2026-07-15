<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MinistryResource extends JsonResource
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
            'course_id' => $this->course_id,
            'course_class_id' => $this->course_class_id,
            'full_name' => $this->full_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'is_church_member' => (bool) $this->is_church_member,
            'zone_id' => $this->zone_id,
            'cell_name' => $this->cell_name,
            'observations' => $this->observations,
            'status' => $this->status,
            'created_at' => $this->created_at?->toIso8601String(),
            'course' => new EventResource($this->whenLoaded('course')),
            'zone' => new ZoneResource($this->whenLoaded('zone')),
        ];
    }
}
