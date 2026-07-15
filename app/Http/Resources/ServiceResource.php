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
            'adults_members' => (int) $this->adults_members,
            'adults_visitors' => (int) $this->adults_visitors,
            'adults_salvations' => (int) $this->adults_salvations,
            'children_members' => (int) $this->children_members,
            'children_visitors' => (int) $this->children_visitors,
            'children_salvations' => (int) $this->children_salvations,
            'special_offerings_total' => (float) $this->special_offerings_total,
            'total_offerings' => (float) $this->total_offerings,
            'created_at' => $this->created_at?->toIso8601String(),
            'preacher' => new UserResource($this->whenLoaded('preacher')),
        ];
    }
}
