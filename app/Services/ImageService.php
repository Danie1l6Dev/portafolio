<?php

declare(strict_types=1);

namespace App\Services;

use GdImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

class ImageService
{
    private const DECODERS = [
        'image/jpeg' => 'imagecreatefromjpeg',
        'image/png' => 'imagecreatefrompng',
        'image/webp' => 'imagecreatefromwebp',
    ];

    /**
     * Convierte cada carga a WebP y genera una segunda versión para listados.
     * Las rutas optimizadas se distinguen de los archivos históricos para poder
     * usar el original existente mientras se migra el contenido anterior.
     */
    public function store(UploadedFile $file, string $folder): string
    {
        $mime = $file->getMimeType() ?? '';
        $decoder = self::DECODERS[$mime]
            ?? throw new InvalidArgumentException("Tipo de archivo no permitido: {$mime}");
        $sourcePath = $file->getRealPath();

        if ($sourcePath === false) {
            throw new RuntimeException('No se pudo leer la imagen subida.');
        }

        $source = $decoder($sourcePath);
        if (! $source instanceof GdImage) {
            throw new RuntimeException('No se pudo procesar la imagen subida.');
        }

        $directory = 'images/'.trim($folder, '/').'/optimized';
        $path = $directory.'/'.Str::uuid().'.webp';

        try {
            $this->writeVariant($source, $path, $this->detailWidth(), $this->detailQuality());
            $this->writeVariant($source, self::previewPath($path), $this->previewWidth(), $this->previewQuality());
        } catch (\Throwable $exception) {
            $this->delete($path);

            throw $exception;
        } finally {
            imagedestroy($source);
        }

        return $path;
    }

    public static function previewPath(string $path): string
    {
        if (! self::hasOptimizedVariants($path)) {
            return $path;
        }

        return Str::replaceLast('.webp', '-card.webp', $path);
    }

    public static function url(?string $path, bool $preview = false): ?string
    {
        if (blank($path)) {
            return null;
        }

        $normalizedPath = ltrim((string) $path, '/');

        if (str_starts_with($normalizedPath, 'http://') || str_starts_with($normalizedPath, 'https://')) {
            return $normalizedPath;
        }

        if (str_starts_with($normalizedPath, 'storage/')) {
            return asset($normalizedPath);
        }

        $publicPath = $preview ? self::previewPath($normalizedPath) : $normalizedPath;

        return Storage::disk('public')->url($publicPath);
    }

    /** Borra una imagen optimizada y, cuando existe, su variante ligera. */
    public function delete(?string $path): void
    {
        if (blank($path) || str_starts_with((string) $path, 'http://') || str_starts_with((string) $path, 'https://')) {
            return;
        }

        $normalizedPath = ltrim((string) $path, '/');
        $disk = Storage::disk('public');
        $paths = array_unique([$normalizedPath, self::previewPath($normalizedPath)]);

        foreach ($paths as $storedPath) {
            if ($disk->exists($storedPath)) {
                $disk->delete($storedPath);
            }
        }
    }

    /** @return array{mime_type: string, size: int|null} */
    public function metadata(string $path): array
    {
        $disk = Storage::disk('public');

        return [
            'mime_type' => 'image/webp',
            'size' => $disk->exists($path) ? $disk->size($path) : null,
        ];
    }

    /**
     * Almacena múltiples imágenes para una galería.
     *
     * @param  list<UploadedFile>  $files
     * @return list<string>
     */
    public function storeMany(array $files, string $folder): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file->isValid()) {
                $paths[] = $this->store($file, $folder);
            }
        }

        return $paths;
    }

    private static function hasOptimizedVariants(string $path): bool
    {
        return str_contains($path, '/optimized/') && str_ends_with($path, '.webp');
    }

    private function writeVariant(GdImage $source, string $path, int $maxWidth, int $quality): void
    {
        $sourceWidth = imagesx($source);
        $sourceHeight = imagesy($source);
        $targetWidth = max(1, min($sourceWidth, $maxWidth));
        $targetHeight = max(1, (int) round(($sourceHeight / max(1, $sourceWidth)) * $targetWidth));
        $canvas = imagecreatetruecolor($targetWidth, $targetHeight);

        if ($canvas === false) {
            throw new RuntimeException('No se pudo preparar la imagen optimizada.');
        }

        imagealphablending($canvas, false);
        imagesavealpha($canvas, true);
        $transparent = imagecolorallocatealpha($canvas, 0, 0, 0, 127);

        if ($transparent === false) {
            imagedestroy($canvas);

            throw new RuntimeException('No se pudo preparar la imagen optimizada.');
        }

        imagefilledrectangle($canvas, 0, 0, $targetWidth, $targetHeight, $transparent);
        imagecopyresampled($canvas, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $sourceWidth, $sourceHeight);

        $temporaryPath = tempnam(sys_get_temp_dir(), 'portfolio-webp-');
        if ($temporaryPath === false) {
            imagedestroy($canvas);

            throw new RuntimeException('No se pudo preparar la imagen optimizada.');
        }

        try {
            if (! imagewebp($canvas, $temporaryPath, $quality)) {
                throw new RuntimeException('No se pudo convertir la imagen a WebP.');
            }

            $stream = fopen($temporaryPath, 'rb');
            if ($stream === false) {
                throw new RuntimeException('No se pudo leer la imagen optimizada.');
            }

            try {
                if (! Storage::disk('public')->writeStream($path, $stream)) {
                    throw new RuntimeException('No se pudo guardar la imagen optimizada.');
                }
            } finally {
                fclose($stream);
            }
        } finally {
            imagedestroy($canvas);
            @unlink($temporaryPath);
        }
    }

    private function detailWidth(): int
    {
        return (int) config('admin.images.detail_max_width', 1920);
    }

    private function previewWidth(): int
    {
        return (int) config('admin.images.preview_max_width', 960);
    }

    private function detailQuality(): int
    {
        return (int) config('admin.images.detail_webp_quality', 82);
    }

    private function previewQuality(): int
    {
        return (int) config('admin.images.preview_webp_quality', 76);
    }
}
