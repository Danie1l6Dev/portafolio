<?php

namespace App\Models;

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
        'is_featured' => 'boolean',
        'level'       => 'integer',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
