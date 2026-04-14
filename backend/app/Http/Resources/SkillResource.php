<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SkillResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'name'        => $this->name,
            'slug'        => $this->slug,
            'group'       => $this->group,
            'level'       => $this->level,        // 1-5
            'icon'        => $this->icon,
            'is_featured' => $this->is_featured,
        ];
    }
}
