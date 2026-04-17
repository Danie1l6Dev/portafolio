<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Skill extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'group',
        'level',
        'icon',
        'sort_order',
        'is_featured',
    ];

    protected $casts = [
        'level'       => 'integer',
        'sort_order'  => 'integer',
        'is_featured' => 'boolean',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class, 'project_skill');
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Solo skills marcadas como destacadas. */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /** Agrupadas por group, luego sort_order, luego name. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('group')->orderBy('sort_order')->orderBy('name');
    }

    /** Filtra por grupo (ej: 'Frontend', 'Backend'). */
    public function scopeInGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }
}
