<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    /**
     * mediable_type y mediable_id se excluyen del fillable:
     * Eloquent los gestiona automáticamente a través de la relación morphTo.
     */
    protected $fillable = [
        'collection',
        'disk',
        'path',
        'filename',
        'mime_type',
        'size',
        'alt',
        'sort_order',
    ];

    protected $casts = [
        'size'       => 'integer',
        'sort_order' => 'integer',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Accessors (estilo moderno Laravel 9+) ─────────────────

    /** URL pública del archivo según su disco de almacenamiento. */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: fn () => Storage::disk($this->disk)->url($this->path),
        );
    }

    /** Indica si el archivo es una imagen según su MIME type. */
    protected function isImage(): Attribute
    {
        return Attribute::make(
            get: fn () => str_starts_with((string) $this->mime_type, 'image/'),
        );
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Filtra por colección (ej: 'gallery', 'cover'). */
    public function scopeInCollection(Builder $query, string $collection): Builder
    {
        return $query->where('collection', $collection);
    }
}
