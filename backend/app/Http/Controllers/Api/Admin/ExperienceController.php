<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreExperienceRequest;
use App\Http\Requests\Admin\UpdateExperienceRequest;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use App\Services\ImageService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExperienceController extends Controller
{
    public function __construct(private readonly ImageService $imageService)
    {
    }

    /**
     * GET /api/v1/admin/experiences
     */
    public function index(): AnonymousResourceCollection
    {
        $experiences = Experience::query()
            ->with('media')
            ->ordered()
            ->get();

        return ExperienceResource::collection($experiences);
    }

    /**
     * POST /api/v1/admin/experiences
     */
    public function store(StoreExperienceRequest $request): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('company_logo')) {
            $data['company_logo'] = $this->imageService->store(
                $request->file('company_logo'),
                'experiences'
            );
        }

        // Si is_current es true, asegurar que finished_at sea null
        if (! empty($data['is_current'])) {
            $data['finished_at'] = null;
        }

        $experience = Experience::create($data);

        return response()->json([
            'data'    => ExperienceResource::make($experience),
            'message' => 'Experiencia creada correctamente.',
        ], 201);
    }

    /**
     * GET /api/v1/admin/experiences/{experience}
     */
    public function show(Experience $experience): JsonResponse
    {
        $experience->load('media');

        return response()->json([
            'data' => ExperienceResource::make($experience),
        ]);
    }

    /**
     * PUT/PATCH /api/v1/admin/experiences/{experience}
     */
    public function update(UpdateExperienceRequest $request, Experience $experience): JsonResponse
    {
        $data = $request->validated();

        if ($request->hasFile('company_logo')) {
            $this->imageService->delete($experience->company_logo);
            $data['company_logo'] = $this->imageService->store(
                $request->file('company_logo'),
                'experiences'
            );
        }

        if (! empty($data['is_current'])) {
            $data['finished_at'] = null;
        }

        $experience->update($data);

        return response()->json([
            'data'    => ExperienceResource::make($experience->fresh()->load('media')),
            'message' => 'Experiencia actualizada correctamente.',
        ]);
    }

    /**
     * DELETE /api/v1/admin/experiences/{experience}
     */
    public function destroy(Experience $experience): JsonResponse
    {
        $this->imageService->delete($experience->company_logo);

        foreach ($experience->media as $media) {
            $this->imageService->delete($media->path);
            $media->delete();
        }

        $experience->delete();

        return response()->json([
            'message' => 'Experiencia eliminada correctamente.',
        ]);
    }
}
