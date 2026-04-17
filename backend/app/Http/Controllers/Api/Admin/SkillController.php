<?php

namespace App\Http\Controllers\Api\Admin;

use App\Enums\SkillGroup;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreSkillRequest;
use App\Http\Requests\Admin\UpdateSkillRequest;
use App\Http\Resources\SkillResource;
use App\Models\Skill;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Str;

class SkillController extends Controller
{
    /**
     * GET /api/v1/admin/skills
     */
    public function index(): AnonymousResourceCollection
    {
        $skills = Skill::query()
            ->withCount('projects')
            ->ordered()
            ->get();

        return SkillResource::collection($skills)
            ->additional([
                'meta' => [
                    'groups' => SkillGroup::values(),
                ],
            ]);
    }

    /**
     * POST /api/v1/admin/skills
     */
    public function store(StoreSkillRequest $request): JsonResponse
    {
        $data         = $request->validated();
        $data['slug'] = $this->uniqueSlug($data['name']);

        $skill = Skill::create($data);

        return response()->json([
            'data'    => SkillResource::make($skill),
            'message' => 'Habilidad creada correctamente.',
        ], 201);
    }

    /**
     * GET /api/v1/admin/skills/{skill}
     */
    public function show(Skill $skill): JsonResponse
    {
        $skill->loadCount('projects');

        return response()->json([
            'data' => SkillResource::make($skill),
        ]);
    }

    /**
     * PUT/PATCH /api/v1/admin/skills/{skill}
     */
    public function update(UpdateSkillRequest $request, Skill $skill): JsonResponse
    {
        $data = $request->validated();

        if (isset($data['name']) && $data['name'] !== $skill->name) {
            $data['slug'] = $this->uniqueSlug($data['name'], $skill->id);
        }

        $skill->update($data);

        return response()->json([
            'data'    => SkillResource::make($skill->fresh()),
            'message' => 'Habilidad actualizada correctamente.',
        ]);
    }

    /**
     * DELETE /api/v1/admin/skills/{skill}
     */
    public function destroy(Skill $skill): JsonResponse
    {
        // Desvincula proyectos (pivot cascadeOnDelete ya lo hace en DB,
        // pero lo hacemos explícito para claridad)
        $skill->projects()->detach();
        $skill->delete();

        return response()->json([
            'message' => 'Habilidad eliminada correctamente.',
        ]);
    }

    // ── Helpers ───────────────────────────────────────────────

    private function uniqueSlug(string $name, ?int $excludeId = null): string
    {
        $base = Str::slug($name);
        $slug = $base;
        $i    = 1;

        while (
            Skill::where('slug', $slug)
                ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                ->exists()
        ) {
            $slug = "{$base}-{$i}";
            $i++;
        }

        return $slug;
    }
}
