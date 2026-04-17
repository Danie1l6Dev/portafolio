<?php

namespace App\Traits;

use Illuminate\Support\Str;

/**
 * Genera slugs únicos para cualquier modelo que tenga una columna `slug`.
 *
 * Uso:
 *   use App\Traits\HasSlug;
 *
 *   class Project extends Model
 *   {
 *       use HasSlug;
 *       protected string $slugSource = 'title'; // columna origen del slug
 *   }
 *
 * Luego en el controlador:
 *   $data['slug'] = $model->generateSlug($title);
 *   // o para update (excluye el ID actual):
 *   $data['slug'] = $model->generateSlug($title, $model->id);
 */
trait HasSlug
{
    /**
     * Genera un slug único para este modelo.
     *
     * @param  string    $value     Texto origen del slug (ej: título del proyecto)
     * @param  int|null  $excludeId ID a excluir de la comprobación de unicidad (para updates)
     */
    public function generateSlug(string $value, ?int $excludeId = null): string
    {
        $base = Str::slug($value);
        $slug = $base;
        $i    = 1;

        while (
            static::where('slug', $slug)
                ->when($excludeId !== null, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }

    /**
     * Genera un slug desde el valor actual de la columna origen (slugSource).
     * Útil en boot hooks o mutators.
     */
    public function generateSlugFromAttribute(): string
    {
        $source = $this->{$this->slugSource ?? 'name'};

        return $this->generateSlug($source, $this->exists ? $this->id : null);
    }
}
