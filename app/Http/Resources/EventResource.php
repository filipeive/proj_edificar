<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'description' => $this->description,
            'date' => $this->date,
            'time' => $this->time,
            'location' => $this->location,
            'zone_id' => $this->zone_id,
            'cell_id' => $this->cell_id,
            'capacity' => $this->capacity,
            'registration_open' => (bool) $this->registration_open,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
