<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ContributionResource extends JsonResource
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
            'user_id' => $this->user_id,
            'amount' => (float) $this->amount,
            'cell_id' => $this->cell_id,
            'supervision_id' => $this->supervision_id,
            'zone_id' => $this->zone_id,
            'proof_path' => $this->proof_path,
            'proof_message' => $this->proof_message,
            'contribution_date' => $this->contribution_date?->toDateString(),
            'package_id' => $this->package_id,
            'notes' => $this->notes,
            'status' => $this->status,
            'verified_by_id' => $this->verified_by_id,
            'rejection_reason' => $this->rejection_reason,
            'created_at' => $this->created_at?->toIso8601String(),
            'user' => new UserResource($this->whenLoaded('user')),
            'cell' => new CellResource($this->whenLoaded('cell')),
            'package' => new PackageResource($this->whenLoaded('package')),
        ];
    }
}
