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
use Illuminate\Support\Facades\DB;
use Throwable;

class ProjectController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $projects = Project::query()
            ->with(['category', 'skills'])
            ->when(
                $request->filled('status'),
                fn ($query) => $query->withStatus($request->status),
            )
            ->when(
                $request->filled('category'),
                fn ($query) => $query->whereHas(
                    'category',
                    fn ($query) => $query->where('slug', $request->category),
                ),
            )
            ->ordered()
            ->paginate(perPage: 15, page: $request->integer('page', 1))
            ->withQueryString();

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $data = $request->validated();
        $data['slug'] = (new Project)->generateSlug($data['title']);

        $skillIds = $data['skill_ids'] ?? [];
        $galleryImages = $request->file('gallery_images', []);
        $galleryImages = is_array($galleryImages) ? $galleryImages : [$galleryImages];
        unset($data['skill_ids'], $data['gallery_images'], $data['cover_image']);

        $storedPaths = [];

        try {
            $project = DB::transaction(function () use ($request, $data, $skillIds, $galleryImages, &$storedPaths): Project {
                if ($request->hasFile('cover_image')) {
                    $coverPath = $this->imageService->store($request->file('cover_image'), 'projects');
                    $storedPaths[] = $coverPath;
                    $data['cover_image'] = $coverPath;
                }

                $project = Project::create($data);
                $project->skills()->sync($skillIds);
                $sortOrder = $project->media()->inCollection('gallery')->max('sort_order') ?? 0;

                foreach ($galleryImages as $image) {
                    $path = $this->imageService->store($image, 'projects/gallery');
                    $storedPaths[] = $path;

                    $project->media()->create([
                        'collection' => 'gallery',
                        'disk' => 'public',
                        'path' => $path,
                        'filename' => $image->getClientOriginalName(),
                        'mime_type' => $image->getMimeType(),
                        'size' => $image->getSize(),
                        'sort_order' => ++$sortOrder,
                    ]);
                }

                return $project;
            });
        } catch (Throwable $exception) {
            $this->deleteStoredPaths($storedPaths);

            throw $exception;
        }

        $project->load(['category', 'skills', 'media']);

        return response()->json([
            'data' => ProjectResource::make($project),
            'message' => 'Proyecto creado correctamente.',
        ], 201);
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['category', 'skills', 'media']);

        return response()->json([
            'data' => ProjectResource::make($project),
        ]);
    }

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['title']) && $data['title'] !== $project->title) {
            $data['slug'] = $project->generateSlug($data['title'], $project->id);
        }

        $skillIds = isset($data['skill_ids'])
            ? array_values(array_filter($data['skill_ids'], fn ($value) => $value !== '' && $value !== null))
            : null;
        $galleryImages = $request->file('gallery_images', []);
        $galleryImages = is_array($galleryImages) ? $galleryImages : [$galleryImages];
        unset($data['skill_ids'], $data['gallery_images'], $data['cover_image']);

        $oldCoverPath = $project->cover_image;
        $storedPaths = [];

        try {
            DB::transaction(function () use ($request, $project, $data, $skillIds, $galleryImages, &$storedPaths): void {
                if ($request->hasFile('cover_image')) {
                    $coverPath = $this->imageService->store($request->file('cover_image'), 'projects');
                    $storedPaths[] = $coverPath;
                    $data['cover_image'] = $coverPath;
                }

                $project->update($data);

                if ($skillIds !== null) {
                    $project->skills()->sync($skillIds);
                }

                $sortOrder = $project->media()->inCollection('gallery')->max('sort_order') ?? 0;

                foreach ($galleryImages as $image) {
                    $path = $this->imageService->store($image, 'projects/gallery');
                    $storedPaths[] = $path;

                    $project->media()->create([
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
            $this->deleteStoredPaths($storedPaths);

            throw $exception;
        }

        if ($request->hasFile('cover_image') && $oldCoverPath !== $project->cover_image) {
            $this->imageService->delete($oldCoverPath);
        }

        $project->load(['category', 'skills', 'media']);

        return response()->json([
            'data' => ProjectResource::make($project),
            'message' => 'Proyecto actualizado correctamente.',
        ]);
    }

    public function destroy(Project $project): JsonResponse
    {
        $paths = $project->media->pluck('path')->push($project->cover_image)->filter()->all();

        DB::transaction(function () use ($project): void {
            $project->media()->delete();
            $project->delete();
        });

        $this->deleteStoredPaths($paths);

        return response()->json([
            'message' => 'Proyecto eliminado correctamente.',
        ]);
    }

    /** @param array<int, string> $paths */
    private function deleteStoredPaths(array $paths): void
    {
        foreach (array_reverse($paths) as $path) {
            $this->imageService->delete($path);
        }
    }
}
