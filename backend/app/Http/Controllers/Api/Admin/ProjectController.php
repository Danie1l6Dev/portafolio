<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProjectRequest;
use App\Http\Requests\Admin\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class ProjectController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    /**
     * GET /api/v1/admin/projects
     * Lista TODOS los proyectos (incluye draft y archived). Paginado.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->with(['category', 'skills'])
            ->when(
                $request->filled('status'),
                fn ($q) => $q->withStatus($request->status)
            )
            ->when(
                $request->filled('category'),
                fn ($q) => $q->whereHas(
                    'category',
                    fn ($q) => $q->where('slug', $request->category)
                )
            )
            ->ordered()
            ->paginate(perPage: 15, page: $request->integer('page', 1))
            ->withQueryString();

        return ProjectResource::collection($projects);
    }

    /**
     * POST /api/v1/admin/projects
     */
    public function store(StoreProjectRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Slug único generado desde el título
        $data['slug'] = $this->uniqueSlug($data['title']);

        // Subida de imagen de portada
        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $this->imageService->store(
                $request->file('cover_image'),
                'projects'
            );
        }

        $skillIds = $data['skill_ids'] ?? [];
        unset($data['skill_ids']);

        $project = Project::create($data);

        // Sincronizar habilidades
        if (! empty($skillIds)) {
            $project->skills()->sync($skillIds);
        }

        $project->load(['category', 'skills', 'media']);

        return response()->json([
            'data'    => ProjectResource::make($project),
            'message' => 'Proyecto creado correctamente.',
        ], 201);
    }

    /**
     * GET /api/v1/admin/projects/{project}
     */
    public function show(Project $project): JsonResponse
    {
        $project->load(['category', 'skills', 'media']);

        return response()->json([
            'data' => ProjectResource::make($project),
        ]);
    }

    /**
     * PUT/PATCH /api/v1/admin/projects/{project}
     */
    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $data = $request->validated();

        // Regenerar slug si el título cambió
        if (isset($data['title']) && $data['title'] !== $project->title) {
            $data['slug'] = $this->uniqueSlug($data['title'], $project->id);
        }

        // Nueva imagen: borrar la anterior y subir la nueva
        if ($request->hasFile('cover_image')) {
            $this->imageService->delete($project->cover_image);
            $data['cover_image'] = $this->imageService->store(
                $request->file('cover_image'),
                'projects'
            );
        }

        $skillIds = $data['skill_ids'] ?? null;
        unset($data['skill_ids']);

        $project->update($data);

        // Sincronizar habilidades si se enviaron
        if (! is_null($skillIds)) {
            $project->skills()->sync($skillIds);
        }

        $project->load(['category', 'skills', 'media']);

        return response()->json([
            'data'    => ProjectResource::make($project),
            'message' => 'Proyecto actualizado correctamente.',
        ]);
    }

    /**
     * DELETE /api/v1/admin/projects/{project}
     */
    public function destroy(Project $project): JsonResponse
    {
        // Borrar imagen de portada
        $this->imageService->delete($project->cover_image);

        // Borrar media adjunta (galería)
        foreach ($project->media as $media) {
            $this->imageService->delete($media->path);
            $media->delete();
        }

        $project->delete();

        return response()->json([
            'message' => 'Proyecto eliminado correctamente.',
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function uniqueSlug(string $title, ?int $excludeId = null): string
    {
        $base = Str::slug($title);
        $slug = $base;
        $i    = 1;

        while (
            Project::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
