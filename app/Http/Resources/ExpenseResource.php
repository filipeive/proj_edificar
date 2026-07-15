<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'requisition_id' => $this->requisition_id,
            'description' => $this->description,
            'amount' => (float) $this->amount,
            'date' => $this->date?->toDateString(),
            'category' => $this->category,
            'scope' => $this->scope,
            'user_id' => $this->user_id,
            'created_at' => $this->created_at?->toIso8601String(),
            'requisition' => new RequisitionResource($this->whenLoaded('requisition')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
