<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WeddingResource extends JsonResource
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
            'groom_name' => $this->groom_name,
            'bride_name' => $this->bride_name,
            'date' => $this->date?->toDateString(),
            'time' => $this->time?->toTimeString(),
            'location' => $this->location,
            'godparents' => $this->godparents,
            'status' => $this->status,
            'observations' => $this->observations,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
