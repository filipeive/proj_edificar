<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PackageResource extends JsonResource
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
            'min_amount' => (float) $this->min_amount,
            'max_amount' => (float) $this->max_amount,
            'description' => $this->description,
            'whatsapp_link' => $this->whatsapp_link,
            'sms_template' => $this->sms_template,
            'whatsapp_template' => $this->whatsapp_template,
            'is_active' => (bool) $this->is_active,
            'order' => (int) $this->order,
            'responsible_id' => $this->responsible_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'responsible' => new UserResource($this->whenLoaded('responsible')),
        ];
    }
}
