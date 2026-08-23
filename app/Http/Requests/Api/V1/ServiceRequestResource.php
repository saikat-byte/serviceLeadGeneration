<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceRequestResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'status'       => $this->status->value ?? $this->status,
            'urgency'      => $this->urgency->value ?? $this->urgency,
            'description'  => $this->description,
            'budget_min'   => $this->budget_min,
            'budget_max'   => $this->budget_max,
            'preferred_at' => $this->preferred_at?->format('Y-m-d H:i:s'),
            'created_at'   => $this->created_at->format('Y-m-d H:i:s'),
            
            // Relationships
            'service'      => new ServiceResource($this->whenLoaded('service')),
            'variant'      => new ServiceVariantResource($this->whenLoaded('serviceVariant')),
        ];
    }
}