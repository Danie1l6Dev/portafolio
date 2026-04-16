<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreCategoryRequest;
use App\Http\Requests\Admin\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * GET /api/v1/admin/categories
     * Lista todas las categorías con conteo de proyectos asociados.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->withCount('projects')
            ->ordered()
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * POST /api/v1/admin/categories
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $data         = $request->validated();
        $data['slug'] = (new Category)->generateSlug($data['name']);

        $category = Category::create($data);

        return response()->json([
            'data'    => CategoryResource::make($category),
            'message' => 'Categoría creada correctamente.',
        ], 201);
    }

    /**
     * GET /api/v1/admin/categories/{category}
     */
    public function show(Category $category): JsonResponse
    {
        $category->loadCount('projects');

        return response()->json([
            'data' => CategoryResource::make($category),
        ]);
    }

    /**
     * PUT/PATCH /api/v1/admin/categories/{category}
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $data = $request->validated();

        // Regenerar slug si el nombre cambió
        if (isset($data['name']) && $data['name'] !== $category->name) {
            $data['slug'] = $category->generateSlug($data['name'], $category->id);
        }

        $category->update($data);

        return response()->json([
            'data'    => CategoryResource::make($category->fresh()),
            'message' => 'Categoría actualizada correctamente.',
        ]);
    }

    /**
     * DELETE /api/v1/admin/categories/{category}
     * No elimina si tiene proyectos asociados (la FK está en nullOnDelete,
     * pero informamos al admin antes de actuar).
     */
    public function destroy(Category $category): JsonResponse
    {
        $projectsCount = $category->projects()->count();

        if ($projectsCount > 0) {
            return response()->json([
                'message' => "No se puede eliminar: tiene {$projectsCount} proyecto(s) asociado(s). Reasígnalos primero.",
            ], 422);
        }

        $category->delete();

        return response()->json([
            'message' => 'Categoría eliminada correctamente.',
        ]);
    }
}
