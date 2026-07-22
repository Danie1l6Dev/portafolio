<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

final class DocumentService
{
    private const PDF_MIME_TYPES = [
        'application/pdf',
        'application/x-pdf',
    ];

    public function storePdf(UploadedFile $file, string $folder): string
    {
        $mimeType = $file->getMimeType() ?? '';

        if (! in_array($mimeType, self::PDF_MIME_TYPES, true)) {
            throw new InvalidArgumentException("Tipo de documento no permitido: {$mimeType}");
        }

        $path = $file->storeAs(
            "documents/{$folder}",
            Str::uuid()->toString().'.pdf',
            'public',
        );

        if ($path === false) {
            throw new RuntimeException('No se pudo almacenar el documento en el disco público.');
        }

        return $path;
    }

    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
