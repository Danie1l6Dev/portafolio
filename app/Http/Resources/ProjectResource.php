<?php

namespace App\Http\Resources;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Project */
class ProjectResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        // La descripción completa se incluye en el detalle público (show)
        // y en todos los endpoints del panel admin.
        $includeDescription = $request->routeIs('backend.projects.show')
            || $request->routeIs('admin.projects.*');

        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'summary' => $this->summary,
            'description' => $this->when($includeDescription, $this->description),
            'demo_url' => $this->demo_url,
            'repo_url' => $this->repo_url,
            'cover_image' => $this->cover_image,
            'status' => $this->status,
            'is_featured' => $this->is_featured,
            'sort_order' => $this->sort_order,
            'started_at' => $this->started_at?->toDateString(),
            'finished_at' => $this->finished_at?->toDateString(),
            'in_progress' => $this->isInProgress(),

            // Relaciones – solo presentes si fueron cargadas (evita N+1)
            'category' => CategoryResource::make($this->whenLoaded('category')),
            'skills' => SkillResource::collection($this->whenLoaded('skills')),
            'media' => MediaResource::collection($this->whenLoaded('media')),
        ];
    }
}
