<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CategoryResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'description'    => $this->description,
            'color'          => $this->color,
            'sort_order'     => $this->sort_order,
            // projects_count solo está disponible cuando se llama withCount()
            'projects_count' => $this->whenCounted('projects'),
        ];
    }
}
