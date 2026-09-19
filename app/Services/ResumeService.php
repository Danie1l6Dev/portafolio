<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class ResumeService
{
    /** Ruta fija en el disco público: cada subida reemplaza a la anterior. */
    public const string PATH = 'documents/hoja-de-vida.pdf';

    public function hasUpload(): bool
    {
        return Storage::disk('public')->exists(self::PATH);
    }

    /** Guarda el PDF subido como la hoja de vida vigente. */
    public function store(UploadedFile $file): void
    {
        $stream = fopen((string) $file->getRealPath(), 'rb');

        if ($stream === false) {
            throw new RuntimeException('No se pudo leer el PDF subido.');
        }

        try {
            if (! Storage::disk('public')->writeStream(self::PATH, $stream)) {
                throw new RuntimeException('No se pudo guardar la hoja de vida.');
            }
        } finally {
            fclose($stream);
        }
    }

    /** Elimina la subida y vuelve al PDF incluido en el proyecto. */
    public function reset(): void
    {
        Storage::disk('public')->delete(self::PATH);
    }

    /**
     * URL de descarga vigente. Añade la fecha de modificación para que los
     * navegadores no sirvan una versión anterior desde caché.
     */
    public function url(): string
    {
        if (! $this->hasUpload()) {
            return asset((string) config('portfolio.resume.path'));
        }

        $disk = Storage::disk('public');

        return $disk->url(self::PATH).'?v='.$disk->lastModified(self::PATH);
    }

    /** @return array{source: 'uploaded'|'default', url: string, size: int|null, updated_at: int|null} */
    public function current(): array
    {
        if ($this->hasUpload()) {
            $disk = Storage::disk('public');

            return [
                'source' => 'uploaded',
                'url' => $this->url(),
                'size' => $disk->size(self::PATH),
                'updated_at' => $disk->lastModified(self::PATH),
            ];
        }

        $defaultPath = public_path((string) config('portfolio.resume.path'));
        $exists = is_file($defaultPath);

        return [
            'source' => 'default',
            'url' => $this->url(),
            'size' => $exists ? (int) filesize($defaultPath) : null,
            'updated_at' => $exists ? (int) filemtime($defaultPath) : null,
        ];
    }
}
