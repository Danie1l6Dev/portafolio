<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'group'          => $this->group,
            'level'          => $this->level,
            'icon'           => $this->icon,
            'sort_order'     => $this->sort_order,
            'is_featured'    => $this->is_featured,
            // Solo presente cuando se llama withCount() desde el admin
            'projects_count' => $this->whenCounted('projects'),
        ];
    }
}
