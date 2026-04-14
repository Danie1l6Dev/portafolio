<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Orden de presentación definido por sort_order, luego name. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }
}
