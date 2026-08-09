<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PublicStaffResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'full_name' => $this->full_name,
            'slug' => $this->slug,
            'specialty' => $this->specialty,
            'avatar_url' => $this->avatar_url,
            'bio' => $this->bio,
            'experience_years' => $this->experience_years,
            'services' => $this->whenLoaded('services', fn () => $this->services->map(fn ($service) => [
                'id' => $service->id,
                'name' => $service->name,
                'duration_minutes' => $service->duration_minutes,
            ])),
        ];
    }
}
