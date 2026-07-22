<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Http\Resources\ProjectResource;
use App\Models\Media;
use App\Models\Project;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Throwable;

class MediaController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    /**
     * POST /admin/projects/{project}/media
     * Sube múltiples imágenes a la galería del proyecto.
     */
    public function store(Request $request, int $projectId): JsonResponse
    {
        $request->validate([
            'images' => ['required', 'array', 'min:1', 'max:8'],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'collection' => ['nullable', Rule::in(['gallery'])],
        ]);

        $project = Project::findOrFail($projectId);
        $images = $request->file('images', []);
        if (! is_array($images)) {
            $images = [$images];
        }

        $existingCount = $project->media()->inCollection('gallery')->count();

        if ($existingCount + count($images) > 8) {
            throw ValidationException::withMessages([
                'images' => 'La galería admite un máximo total de 8 imágenes.',
            ]);
        }

        $mediaItems = [];
        $storedPaths = [];

        try {
            DB::transaction(function () use ($project, $images, &$mediaItems, &$storedPaths): void {
                $sortOrder = $project->media()->inCollection('gallery')->max('sort_order') ?? 0;

                foreach ($images as $image) {
                    $path = $this->imageService->store($image, 'projects/gallery');
                    $storedPaths[] = $path;

                    $mediaItems[] = $project->media()->create([
                        'collection' => 'gallery',
                        'disk' => 'public',
                        'path' => $path,
                        'filename' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'sort_order' => ++$sortOrder,
                    ]);
                }
            });
        } catch (Throwable $exception) {
            foreach (array_reverse($storedPaths) as $path) {
                $this->imageService->delete($path);
            }

            throw $exception;
        }

        return response()->json([
            'data' => MediaResource::collection($mediaItems),
            'message' => count($mediaItems).' imagen(es) subida(s) correctamente.',
        ], 201);
    }

    /**
     * DELETE /admin/media/{media}
     * Elimina una imagen de la galería.
     */
    public function destroy(int $media): JsonResponse
    {
        $mediaItem = Media::findOrFail($media);

        // Verificar que el usuario tiene acceso al proyecto padre
        $project = $mediaItem->mediable;
        if (! $project instanceof Project) {
            return response()->json(['message' => 'Recurso no encontrado.'], 404);
        }

        $this->imageService->delete($mediaItem->path);
        $mediaItem->delete();

        return response()->json([
            'message' => 'Imagen eliminada correctamente.',
        ]);
    }

    /**
     * PUT /admin/media/{media}
     * Actualiza el orden o alt de una imagen.
     */
    public function update(Request $request, int $media): JsonResponse
    {
        $mediaItem = Media::findOrFail($media);

        $request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        $mediaItem->update($request->only(['alt', 'sort_order']));

        return response()->json([
            'data' => $mediaItem,
            'message' => 'Imagen actualizada correctamente.',
        ]);
    }

    /**
     * PATCH /admin/projects/{project}/media/reorder
     * Reordena las imágenes de la galería.
     */
    public function reorder(Request $request, int $projectId): JsonResponse
    {
        $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer'],
        ]);

        $project = Project::findOrFail($projectId);
        $order = $request->input('order', []);

        foreach ($order as $mediaId) {
            $mediaItem = $project->media()->where('id', $mediaId)->first();
            if ($mediaItem) {
                $mediaItem->update(['sort_order' => array_search($mediaId, $order)]);
            }
        }

        return response()->json([
            'message' => 'Orden actualizado correctamente.',
        ]);
    }

    /**
     * PATCH /admin/projects/{project}/cover
     *
     * Intercambia la portada del proyecto con una imagen de la galería:
     *   - La imagen seleccionada deja de estar en `media` y pasa a ser
     *     `cover_image`.
     *   - La portada anterior (si existía) se añade como un nuevo registro
     *     en `media` (collection=gallery), de forma que siga disponible
     *     en la galería.
     *
     * No se eliminan archivos físicos: ambos se reutilizan en su nuevo rol.
     */
    public function setCover(Request $request, int $projectId): JsonResponse
    {
        $request->validate([
            'media_id' => ['required', 'integer'],
        ]);

        $project = Project::findOrFail($projectId);
        $mediaItem = Media::query()
            ->where('mediable_type', $project->getMorphClass())
            ->where('mediable_id', $project->getKey())
            ->findOrFail($request->integer('media_id'));

        $oldCoverPath = $project->cover_image;
        $newCoverPath = $mediaItem->path;

        // 1. Quitar de `media` el registro que pasa a portada (conservamos el archivo).
        $mediaItem->delete();

        // 2. Si había una portada anterior, moverla a la galería creando un nuevo
        //    registro de media que apunte al mismo archivo.
        if ($oldCoverPath) {
            $absolutePath = Storage::disk('public')->path($oldCoverPath);
            $size = is_file($absolutePath) ? (filesize($absolutePath) ?: 0) : 0;
            $mime = is_file($absolutePath) ? (@mime_content_type($absolutePath) ?: null) : null;
            $sortOrder = ($project->media()->max('sort_order') ?? 0) + 1;

            $project->media()->create([
                'collection' => 'gallery',
                'disk' => 'public',
                'path' => $oldCoverPath,
                'filename' => basename($oldCoverPath),
                'mime_type' => $mime,
                'size' => $size,
                'sort_order' => $sortOrder,
            ]);
        }

        // 3. Actualizar la ruta de la portada (sin borrar archivos: ambos se reutilizan).
        $project->update(['cover_image' => $newCoverPath]);

        return response()->json([
            'data' => ProjectResource::make($project->fresh(['category', 'skills', 'media'])),
            'message' => 'Portada actualizada correctamente.',
        ]);
    }
}
