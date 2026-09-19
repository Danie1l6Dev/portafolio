<?php

use App\Services\ImageService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('converts an uploaded image into optimized WebP detail and preview variants', function (): void {
    Storage::fake('public');

    $file = UploadedFile::fake()->image('captura.png', 2400, 1600);
    $imageService = app(ImageService::class);

    $path = $imageService->store($file, 'projects');
    $previewPath = ImageService::previewPath($path);

    expect($path)
        ->toStartWith('images/projects/optimized/')
        ->toEndWith('.webp')
        ->and($previewPath)->toEndWith('-card.webp');

    Storage::disk('public')->assertExists($path);
    Storage::disk('public')->assertExists($previewPath);

    $detailDimensions = getimagesize(Storage::disk('public')->path($path));
    $previewDimensions = getimagesize(Storage::disk('public')->path($previewPath));

    expect($detailDimensions[0])->toBe(1920)
        ->and($detailDimensions[1])->toBe(1280)
        ->and($detailDimensions[2])->toBe(IMAGETYPE_WEBP)
        ->and($previewDimensions[0])->toBe(960)
        ->and($previewDimensions[1])->toBe(640)
        ->and($previewDimensions[2])->toBe(IMAGETYPE_WEBP)
        ->and($imageService->metadata($path))->toMatchArray([
            'mime_type' => 'image/webp',
        ]);

    $imageService->delete($path);

    Storage::disk('public')->assertMissing($path);
    Storage::disk('public')->assertMissing($previewPath);
});

it('builds a srcset with the preview and detail variants for optimized images', function (): void {
    Storage::fake('public');

    $path = app(ImageService::class)->store(UploadedFile::fake()->image('captura.png', 2400, 1600), 'projects');

    $srcset = ImageService::srcset($path);

    expect($srcset)
        ->toContain(ImageService::url($path, true).' 960w')
        ->toContain(ImageService::url($path, false).' 1920w');
});

it('returns no srcset for external urls, legacy paths, or missing images', function (): void {
    expect(ImageService::srcset(null))->toBeNull()
        ->and(ImageService::srcset('https://example.com/imagen.jpg'))->toBeNull()
        ->and(ImageService::srcset('images/historico/foto.jpg'))->toBeNull();
});
