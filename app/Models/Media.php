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
        'size' => 'integer',
        'sort_order' => 'integer',
    ];

    // ── Relaciones ────────────────────────────────────────────

    /** @return MorphTo<Model, $this> */
    public function mediable(): MorphTo
    {
        return $this->morphTo();
    }

    // ── Accessors (estilo moderno Laravel 9+) ─────────────────

    /** URL pública del archivo según su disco de almacenamiento. */
    /** @return Attribute<string, never> */
    protected function url(): Attribute
    {
        return Attribute::make(
            get: function () {
                // Normalizar el path: eliminar slashes duplicados y leading slash
                $path = $this->path;
                $path = preg_replace('#/+#', '/', $path); // /storage//storage → /storage/
                $path = ltrim($path, '/'); // /images/ → images/

                return Storage::url($path);
            }
        );
    }

    /** Indica si el archivo es una imagen según su MIME type. */
    /** @return Attribute<bool, never> */
    protected function isImage(): Attribute
    {
        return Attribute::make(
            get: fn () => str_starts_with((string) $this->mime_type, 'image/'),
        );
    }

    // ── Scopes ────────────────────────────────────────────────

    /**
     * Filtra por colección (ej: 'gallery', 'cover').
     *
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeInCollection(Builder $query, string $collection): Builder
    {
        return $query->where('collection', $collection);
    }
}
