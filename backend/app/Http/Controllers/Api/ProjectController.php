<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProjectController extends Controller
{
    /**
     * GET /api/projects
     *
     * Lista paginada de proyectos publicados.
     * Soporta filtros opcionales por ?category= y ?featured=1.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->published()
            ->with(['category', 'skills'])
            ->when(
                $request->filled('category'),
                fn ($q) => $q->whereHas(
                    'category',
                    fn ($q) => $q->where('slug', $request->category)
                )
            )
            ->when(
                $request->boolean('featured'),
                fn ($q) => $q->featured()
            )
            ->ordered()
            ->paginate(perPage: 9, page: $request->integer('page', 1))
            ->withQueryString();

        return ProjectResource::collection($projects);
    }

    /**
     * GET /api/projects/{project}
     *
     * Detalle completo de un proyecto publicado.
     * El parámetro es el ID (route model binding).
     */
    public function show(Project $project): ProjectResource|JsonResponse
    {
        if (! $project->isPublished()) {
            return response()->json([
                'message' => 'Proyecto no encontrado.',
            ], 404);
        }

        $project->load(['category', 'skills', 'media']);

        return ProjectResource::make($project);
    }
}
