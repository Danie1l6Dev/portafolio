<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'slug'        => $this->slug,
            'summary'     => $this->summary,
            'description' => $this->when(
                $request->routeIs('api.projects.show'),
                $this->description
            ),
            'demo_url'    => $this->demo_url,
            'repo_url'    => $this->repo_url,
            'cover_image' => $this->cover_image,
            'status'      => $this->status,
            'is_featured' => $this->is_featured,
            'started_at'  => $this->started_at?->toDateString(),
            'finished_at' => $this->finished_at?->toDateString(),
            'in_progress' => $this->isInProgress(),

            // Relaciones – solo presentes si fueron cargadas (evita N+1)
            'category'    => CategoryResource::make($this->whenLoaded('category')),
            'skills'      => SkillResource::collection($this->whenLoaded('skills')),
            'media'       => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
