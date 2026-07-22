<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\AchievementType;
use Database\Factories\AchievementFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;

/**
 * @property int $id
 * @property string $title
 * @property AchievementType $type
 * @property string $organization
 * @property string|null $result
 * @property string|null $role
 * @property string|null $description
 * @property Carbon $achieved_at
 * @property string|null $external_url
 * @property string|null $image_path
 * @property string|null $certificate_path
 * @property bool $is_featured
 * @property bool $is_visible
 * @property int $sort_order
 */
final class Achievement extends Model
{
    /** @use HasFactory<AchievementFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'type',
        'organization',
        'result',
        'role',
        'description',
        'achieved_at',
        'external_url',
        'image_path',
        'certificate_path',
        'is_featured',
        'is_visible',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'type' => AchievementType::class,
            'achieved_at' => 'date',
            'is_featured' => 'boolean',
            'is_visible' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    /** @return MorphMany<Media, $this> */
    public function media(): MorphMany
    {
        return $this->morphMany(Media::class, 'mediable')
            ->orderBy('sort_order')
            ->orderBy('id');
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeVisible(Builder $query): Builder
    {
        return $query->where('is_visible', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true);
    }

    /**
     * @param  Builder<static>  $query
     * @return Builder<static>
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('achieved_at')
            ->orderBy('id');
    }

    public function imageUrl(): ?string
    {
        if ($this->image_path) {
            return Storage::disk('public')->url($this->image_path);
        }

        $firstImage = $this->relationLoaded('media')
            ? $this->media->first(fn (Media $media): bool => $media->is_image)
            : $this->media()->where('mime_type', 'like', 'image/%')->first();

        return $firstImage?->url;
    }

    public function certificateUrl(): ?string
    {
        return $this->certificate_path
            ? Storage::disk('public')->url($this->certificate_path)
            : null;
    }
}
