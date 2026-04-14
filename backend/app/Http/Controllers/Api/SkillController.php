<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SkillController extends Controller
{
    /**
     * GET /api/skills
     *
     * Lista todas las habilidades ordenadas por group → sort_order → name.
     * Soporta filtro opcional ?featured=1 para mostrar solo las destacadas.
     * La respuesta incluye un meta con los grupos disponibles para navegación.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $skills = Skill::query()
            ->when(
                $request->boolean('featured'),
                fn ($q) => $q->featured()
            )
            ->when(
                $request->filled('group'),
                fn ($q) => $q->inGroup($request->group)
            )
            ->ordered()
            ->get();

        return SkillResource::collection($skills)
            ->additional([
                'meta' => [
                    'groups' => Skill::query()
                        ->whereNotNull('group')
                        ->distinct()
                        ->orderBy('group')
                        ->pluck('group'),
                ],
            ]);
    }
}
