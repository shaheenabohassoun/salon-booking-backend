<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'audience' => $this->audience,
            'duration_label' => $this->duration_label,
            'duration_minutes' => $this->duration_minutes,
            'price' => (float) $this->price,
            'description' => $this->description,
            'is_featured' => (bool) $this->is_featured,
            'category' => [
                'id' => $this->category?->id,
                'name' => $this->category?->name,
                'audience' => $this->category?->audience,
            ],
            'staff' => $this->whenLoaded('staff', fn () => $this->staff->map(fn ($staff) => [
                'id' => $staff->id,
                'full_name' => $staff->full_name,
                'specialty' => $staff->specialty,
            ])),
        ];
    }
}
