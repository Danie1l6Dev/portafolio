<?php

namespace App\Models;

use App\Enums\ProjectStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'description',
        'status',
        'featured',
        'published_at',
    ];

    protected $casts = [
        'status' => ProjectStatus::class,
        'featured' => 'boolean',
        'published_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    // 1:N -> imágenes
    public function images()
    {
        return $this->hasMany(ProjectImage::class)
            ->orderBy('order');
    }

    // N:N -> tecnologías
    public function technologies()
    {
        return $this->belongsToMany(Technology::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes profesionales
    |--------------------------------------------------------------------------
    */

    public function scopePublished($query)
    {
        return $query->where('status', ProjectStatus::Published);
    }

    public function scopeFeatured($query)
    {
        return $query->where('featured', true);
    }
}
