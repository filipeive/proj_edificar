<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryItemResource extends JsonResource
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
            'category' => $this->category,
            'quantity' => (int) $this->quantity,
            'condition' => $this->condition,
            'location' => $this->location,
            'description' => $this->description,
            'purchased_at' => $this->purchased_at?->toDateString(),
            'value' => (float) $this->value,
            'created_at' => $this->created_at?->toIso8601String(),
        ];
    }
}
