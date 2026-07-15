<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SupervisionResource extends JsonResource
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
            'name' => $this->name,
            'zone_id' => $this->zone_id,
            'supervisor_id' => $this->supervisor_id,
            'sub_supervisor_id' => $this->sub_supervisor_id,
            'display_name' => $this->display_name,
            'created_at' => $this->created_at?->toIso8601String(),
            'zone' => new ZoneResource($this->whenLoaded('zone')),
            'supervisor' => new UserResource($this->whenLoaded('supervisor')),
        ];
    }
}
