<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExperienceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'company'      => $this->company,
            'position'     => $this->position,
            'location'     => $this->location,
            'description'  => $this->description,
            'company_url'  => $this->company_url,
            'company_logo' => $this->company_logo,
            'started_at'   => $this->started_at->toDateString(),
            'finished_at'  => $this->finished_at?->toDateString(),
            'is_current'   => $this->is_current,
            'duration'     => $this->duration,    // accessor: "2021 – Presente"

            // Relaciones
            'media'        => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
