<?php

namespace App\Http\Resources;

use App\Models\Media;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin Media */
class MediaResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'collection' => $this->collection,
            'url' => $this->url,           // accessor: Storage::url(path)
            'path' => $this->path,          // ruta relativa para comparaciones
            'filename' => $this->filename,
            'mime_type' => $this->mime_type,
            'is_image' => $this->is_image,      // accessor: str_starts_with mime
            'alt' => $this->alt,
            'sort_order' => $this->sort_order,
        ];
    }
}
