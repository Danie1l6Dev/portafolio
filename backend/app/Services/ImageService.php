<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageService
{
    /**
     * Almacena una imagen en el disco público y devuelve su ruta relativa.
     *
     * @param  UploadedFile  $file    Archivo subido desde el request
     * @param  string        $folder  Subcarpeta dentro de images/ (ej: 'projects', 'experiences')
     * @return string                 Ruta relativa almacenada en BD (ej: images/projects/uuid.jpg)
     */
    public function store(UploadedFile $file, string $folder): string
    {
        $filename  = Str::uuid()->toString() . '.' . $file->getClientOriginalExtension();
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
