<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * GET /api/v1/categories
     *
     * Lista pública de categorías con proyectos publicados.
     * Útil para construir filtros en el frontend sin pasar por el panel admin.
     * Solo devuelve categorías que tienen al menos un proyecto publicado.
     *
     * También permite que una app móvil u otro cliente construya
     * su propio sistema de filtrado sin depender del endpoint admin.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::query()
            ->whereHas('projects', fn ($q) => $q->published())
            ->withCount(['projects as published_projects_count' => fn ($q) => $q->published()])
            ->ordered()
            ->get();

        return CategoryResource::collection($categories);
    }
}
