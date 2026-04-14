<?php

namespace App\Models;

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
        'started_at'  => 'date',
        'finished_at' => 'date',
    ];

    // ── Relaciones ────────────────────────────────────────────

    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')->orderBy('sort_order');
    }
}
