<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CellResource extends JsonResource
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
            'supervision_id' => $this->supervision_id,
            'leader_id' => $this->leader_id,
            'member_count' => (int) $this->member_count,
            'display_name' => $this->display_name,
            'created_at' => $this->created_at?->toIso8601String(),
            'supervision' => new SupervisionResource($this->whenLoaded('supervision')),
            'leader' => new UserResource($this->whenLoaded('leader')),
        ];
    }
}
