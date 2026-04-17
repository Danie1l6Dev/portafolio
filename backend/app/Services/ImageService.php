<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Mapa MIME → extensión canónica segura.
     * Se usa para derivar la extensión del tipo MIME real del archivo,
     * ignorando la extensión declarada por el cliente (que puede ser falsificada).
     */
    private const MIME_EXTENSIONS = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp',
        'image/gif'  => 'gif',
        'image/avif' => 'avif',
    ];

    /**
     * Almacena una imagen en el disco público y devuelve su ruta relativa.
     *
     * La extensión se deriva del MIME type real del archivo (no del nombre
     * declarado por el cliente) para evitar spoofing de extensiones.
     *
     * @param  UploadedFile  $file    Archivo subido desde el request
     * @param  string        $folder  Subcarpeta dentro de images/ (ej: 'projects', 'experiences')
     * @return string                 Ruta relativa almacenada en BD (ej: images/projects/uuid.jpg)
     *
     * @throws \InvalidArgumentException  Si el MIME type no está en la lista permitida
     */
    public function store(UploadedFile $file, string $folder): string
    {
        $mime      = $file->getMimeType() ?? '';
        $extension = self::MIME_EXTENSIONS[$mime]
            ?? throw new \InvalidArgumentException("Tipo de archivo no permitido: {$mime}");

        $filename  = Str::uuid()->toString() . '.' . $extension;
        $directory = "images/{$folder}";

        return $file->storeAs($directory, $filename, 'public');
    }

    /**
     * Borra una imagen del disco público si existe.
     *
     * @param  string|null  $path  Ruta relativa almacenada en BD
     */
    public function delete(?string $path): void
    {
        if ($path && Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }
}
