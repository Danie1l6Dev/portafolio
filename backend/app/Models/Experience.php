<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Experience extends Model
{
    protected $fillable = [
        'company',
        'position',
        'location',
        'description',
        'company_url',
        'company_logo',
        'started_at',
        'finished_at',
        'is_current',
        'sort_order',
    ];

    protected $casts = [
        'is_current'  => 'boolean',
        'sort_order'  => 'integer',
        'started_at'  => 'date',
        'finished_at' => 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────

    /** Logos e imágenes adjuntas a la experiencia. */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    // ── Scopes ────────────────────────────────────────────────

    /** Solo el empleo actual (finished_at null o is_current = true). */
    public function scopeCurrent(Builder $query): Builder
    {
        return $query->where('is_current', true)->orWhereNull('finished_at');
    }

    /** Orden cronológico inverso: experiencia más reciente primero. */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderByDesc('started_at');
    }

    // ── Helpers ───────────────────────────────────────────────

    /** Duración legible: "2021 – Presente" o "2019 – 2021". */
    public function getDurationAttribute(): string
    {
        $start = $this->started_at->format('Y');
        $end   = $this->is_current || is_null($this->finished_at)
            ? 'Presente'
            : $this->finished_at->format('Y');

        return "{$start} – {$end}";
    }
}
