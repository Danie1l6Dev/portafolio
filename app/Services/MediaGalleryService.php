<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Achievement;
use App\Models\Experience;
use App\Models\Media;
use App\Models\Project;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

final readonly class MediaGalleryService
{
    public const COLLECTION = 'gallery';

    public function __construct(private ImageService $imageService) {}

    /**
     * @param  list<UploadedFile>  $files
     * @return array{paths: list<string>, media: list<Media>}
     */
    public function append(
        Project|Achievement $owner,
        array $files,
        string $folder,
        string $defaultAlt,
    ): array {
        $paths = [];
        $mediaItems = [];
        $sortOrder = (int) ($owner->media()
            ->where('collection', self::COLLECTION)
            ->max('sort_order') ?? 0);

        try {
            DB::transaction(function () use ($owner, $files, $folder, $defaultAlt, &$paths, &$mediaItems, &$sortOrder): void {
                foreach ($files as $file) {
                    if (! $file->isValid()) {
                        continue;
                    }

                    $path = $this->imageService->store($file, $folder);
                    $paths[] = $path;
                    $sortOrder++;

                    $mediaItems[] = $owner->media()->create([
                        'collection' => self::COLLECTION,
                        'disk' => 'public',
                        'path' => $path,
                        'filename' => $file->getClientOriginalName(),
                        'mime_type' => $file->getMimeType(),
                        'size' => $file->getSize(),
                        'alt' => "{$defaultAlt} {$sortOrder}",
                        'sort_order' => $sortOrder,
                    ]);
                }
            });
        } catch (\Throwable $exception) {
            foreach (array_reverse($paths) as $path) {
                $this->imageService->delete($path);
            }

            throw $exception;
        }

        return ['paths' => $paths, 'media' => $mediaItems];
    }

    public function move(Project|Achievement $owner, int $mediaId, int $position): void
    {
        $ids = array_values($owner->media()
            ->where('collection', self::COLLECTION)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn (int|string $id): int => (int) $id)
            ->all());

        $currentPosition = array_search($mediaId, $ids, true);

        if ($currentPosition === false) {
            $owner->media()
                ->where('collection', self::COLLECTION)
                ->findOrFail($mediaId);
        }

        array_splice($ids, (int) $currentPosition, 1);
        $position = max(0, min($position, count($ids)));
        array_splice($ids, $position, 0, [$mediaId]);

        $this->reorder($owner, $ids);
    }

    /** @param list<int> $orderedIds */
    public function reorder(Project|Achievement $owner, array $orderedIds): void
    {
        $currentIds = $owner->media()
            ->where('collection', self::COLLECTION)
            ->orderBy('sort_order')
            ->orderBy('id')
            ->pluck('id')
            ->map(static fn (int|string $id): int => (int) $id)
            ->all();

        if (
            count($orderedIds) !== count(array_unique($orderedIds))
            || count($orderedIds) !== count($currentIds)
            || array_diff($orderedIds, $currentIds) !== []
            || array_diff($currentIds, $orderedIds) !== []
        ) {
            throw ValidationException::withMessages([
                'gallery' => 'El orden debe incluir exactamente las imágenes de esta galería.',
            ]);
        }

        DB::transaction(function () use ($owner, $orderedIds): void {
            foreach ($orderedIds as $index => $mediaId) {
                $owner->media()
                    ->where('collection', self::COLLECTION)
                    ->whereKey($mediaId)
                    ->update(['sort_order' => $index + 1]);
            }
        });
    }

    public function updateAlt(Project|Achievement $owner, int $mediaId, ?string $alt): void
    {
        $media = $owner->media()
            ->where('collection', self::COLLECTION)
            ->findOrFail($mediaId);

        $media->update(['alt' => filled($alt) ? trim((string) $alt) : null]);
    }

    public function delete(Project|Achievement $owner, int $mediaId): void
    {
        $media = $owner->media()
            ->where('collection', self::COLLECTION)
            ->findOrFail($mediaId);
        $path = $media->path;

        DB::transaction(static fn (): bool => $media->delete());

        $this->deletePathsIfUnreferenced([$path]);
    }

    public function promoteToCover(
        Project|Achievement $owner,
        int $mediaId,
        string $coverColumn,
        string $previousCoverAlt,
    ): void {
        $allowedColumn = $owner instanceof Project ? 'cover_image' : 'image_path';

        if ($coverColumn !== $allowedColumn) {
            throw new \InvalidArgumentException('La columna de portada no corresponde al recurso.');
        }

        DB::transaction(function () use ($owner, $mediaId, $coverColumn, $previousCoverAlt): void {
            $lockedOwner = $owner instanceof Project
                ? Project::query()->whereKey($owner->getKey())->lockForUpdate()->firstOrFail()
                : Achievement::query()->whereKey($owner->getKey())->lockForUpdate()->firstOrFail();

            $media = $lockedOwner->media()
                ->where('collection', self::COLLECTION)
                ->lockForUpdate()
                ->findOrFail($mediaId);

            $previousCover = $lockedOwner->getAttribute($coverColumn);
            $newCover = $media->path;

            $media->delete();

            if (filled($previousCover) && $previousCover !== $newCover) {
                $disk = Storage::disk('public');
                $size = $disk->exists($previousCover) ? $disk->size($previousCover) : null;
                $mime = $disk->exists($previousCover) ? $disk->mimeType($previousCover) : null;
                $sortOrder = (int) ($lockedOwner->media()
                    ->where('collection', self::COLLECTION)
                    ->max('sort_order') ?? 0) + 1;

                $lockedOwner->media()->create([
                    'collection' => self::COLLECTION,
                    'disk' => 'public',
                    'path' => $previousCover,
                    'filename' => basename((string) $previousCover),
                    'mime_type' => is_string($mime) ? $mime : null,
                    'size' => is_int($size) ? $size : null,
                    'alt' => $previousCoverAlt,
                    'sort_order' => $sortOrder,
                ]);
            }

            $lockedOwner->update([$coverColumn => $newCover]);
        });
    }

    /** @param list<string|null> $paths */
    public function deletePathsIfUnreferenced(array $paths): void
    {
        foreach (array_values(array_unique(array_filter($paths))) as $path) {
            $referenced = Media::query()
                ->where('disk', 'public')
                ->where('path', $path)
                ->exists()
                || Project::query()->where('cover_image', $path)->exists()
                || Achievement::query()->where('image_path', $path)->orWhere('certificate_path', $path)->exists()
                || Experience::query()->where('company_logo', $path)->exists();

            if (! $referenced) {
                $this->imageService->delete($path);
            }
        }
    }
}
