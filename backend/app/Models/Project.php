<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Project extends Model
{
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
        'started_at'  => 'date',
        'finished_at' => 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function skills(): BelongsToMany
    {
        return $this->belongsToMany(Skill::class);
    }

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }

    // ── Helpers ───────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }
}
