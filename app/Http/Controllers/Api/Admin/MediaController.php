<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\MediaResource;
use App\Http\Resources\ProjectResource;
use App\Models\Media;
use App\Models\Project;
use App\Services\MediaGalleryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class MediaController extends Controller
{
    public function __construct(private readonly MediaGalleryService $galleryService) {}

    /**
     * POST /admin/projects/{project}/media
     * Sube múltiples imágenes a la galería del proyecto.
     */
    public function store(Request $request, int $projectId): JsonResponse
    {
        $galleryLimit = (int) config('admin.galleries.projects.max_items', 8);
        $galleryFileLimit = (int) config('admin.galleries.projects.max_file_kilobytes', 2048);

        $request->validate([
            'images' => ['required', 'array', 'min:1', "max:{$galleryLimit}"],
            'images.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', "max:{$galleryFileLimit}"],
            'collection' => ['nullable', Rule::in(['gallery'])],
        ]);

        $project = Project::findOrFail($projectId);
        $images = $request->file('images', []);
        if (! is_array($images)) {
            $images = [$images];
        }
        $images = array_values($images);

        $existingCount = $project->media()->inCollection('gallery')->count();

        if ($existingCount + count($images) > $galleryLimit) {
            throw ValidationException::withMessages([
                'images' => "La galería admite un máximo total de {$galleryLimit} imágenes.",
            ]);
        }

        $gallery = $this->galleryService->append(
            $project,
            $images,
            'projects/gallery',
            "Captura de {$project->title}",
        );
        $mediaItems = $gallery['media'];

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
        $mediaItem = Media::query()->where('collection', MediaGalleryService::COLLECTION)->findOrFail($media);

        // Verificar que el usuario tiene acceso al proyecto padre
        $project = $mediaItem->mediable;
        if (! $project instanceof Project) {
            return response()->json(['message' => 'Recurso no encontrado.'], 404);
        }

        $this->galleryService->delete($project, $mediaItem->id);

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
        $mediaItem = Media::query()->where('collection', MediaGalleryService::COLLECTION)->findOrFail($media);
        $project = $mediaItem->mediable;

        if (! $project instanceof Project) {
            return response()->json(['message' => 'Recurso no encontrado.'], 404);
        }

        $request->validate([
            'alt' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ]);

        if ($request->has('alt')) {
            $this->galleryService->updateAlt($project, $mediaItem->id, $request->string('alt')->toString());
        }

        if ($request->filled('sort_order')) {
            $this->galleryService->move($project, $mediaItem->id, $request->integer('sort_order'));
        }

        return response()->json([
            'data' => $mediaItem->fresh(),
            'message' => 'Imagen actualizada correctamente.',
        ]);
    }

    /**
     * PATCH /admin/projects/{project}/media/reorder
     * Reordena las imágenes de la galería.
     */
    public function reorder(Request $request, int $projectId): JsonResponse
    {
        $validated = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['required', 'integer', 'distinct'],
        ]);

        $project = Project::findOrFail($projectId);
        $order = array_values(array_map('intval', $validated['order']));
        $this->galleryService->reorder($project, $order);

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
        $this->galleryService->promoteToCover(
            $project,
            $request->integer('media_id'),
            'cover_image',
            "Portada anterior de {$project->title}",
        );

        return response()->json([
            'data' => ProjectResource::make($project->fresh(['category', 'skills', 'media'])),
            'message' => 'Portada actualizada correctamente.',
        ]);
    }
}
