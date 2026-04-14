<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MediaResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'collection' => $this->collection,
            'url'        => $this->url,           // accessor: Storage::url(path)
            'filename'   => $this->filename,
            'mime_type'  => $this->mime_type,
            'is_image'   => $this->is_image,      // accessor: str_starts_with mime
            'alt'        => $this->alt,
            'sort_order' => $this->sort_order,
        ];
    }
}
