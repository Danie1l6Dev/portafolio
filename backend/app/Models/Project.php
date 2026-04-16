<?php

namespace App\Models;

use App\Traits\HasSlug;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
    use HasSlug;

    /** Columna de origen para la generación de slug. */
    protected string $slugSource = 'title';

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'summary',
        'description',
        'demo_url',
        'repo_url',
        'cover_image',
        'status',
        'is_featured',
        'sort_order',
        'started_at',
        'finished_at',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
        'sort_order'  => 'integer',
        'started_at'  => 'date',
        'finished_at' => 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Tabla pivot explícita para evitar ambigüedad con convenciones de nombres.
     * Orden alphabético: project_skill (p < s).
     */
    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class, 'project_skill');
    }

    /** Imágenes y archivos adjuntos al proyecto, ordenadas por sort_order. */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Solo proyectos publicados (visibles en el frontend). */
    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', 'published');
    }

    /** Solo proyectos destacados. */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** Orden de presentación: sort_order ascendente. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('started_at');
    }

    /** Filtra por estado. */
    public function scopeWithStatus(Builder $query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isInProgress(): bool
    {
        return is_null($this->finished_at);
    }
}
