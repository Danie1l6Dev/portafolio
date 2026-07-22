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
use Illuminate\Support\Facades\DB;
use Throwable;

class ExperienceController extends Controller
{
    public function __construct(private readonly ImageService $imageService) {}

    public function index(): AnonymousResourceCollection
    {
        return ExperienceResource::collection(
            Experience::query()->with('media')->ordered()->get(),
        );
    }

    public function store(StoreExperienceRequest $request): JsonResponse
    {
        $data = $request->validated();
        unset($data['company_logo']);

        if (! empty($data['is_current'])) {
            $data['finished_at'] = null;
        }

        $storedLogoPath = null;

        try {
            $experience = DB::transaction(function () use ($request, $data, &$storedLogoPath): Experience {
                if ($request->hasFile('company_logo')) {
                    $storedLogoPath = $this->imageService->store($request->file('company_logo'), 'experiences');
                    $data['company_logo'] = $storedLogoPath;
                }

                return Experience::create($data);
            });
        } catch (Throwable $exception) {
            $this->imageService->delete($storedLogoPath);

            throw $exception;
        }

        return response()->json([
            'data' => ExperienceResource::make($experience),
            'message' => 'Experiencia creada correctamente.',
        ], 201);
    }

    public function show(Experience $experience): JsonResponse
    {
        $experience->load('media');

        return response()->json([
            'data' => ExperienceResource::make($experience),
        ]);
    }

    public function update(UpdateExperienceRequest $request, Experience $experience): JsonResponse
    {
        $data = $request->validated();
        unset($data['company_logo']);

        $effectiveIsCurrent = array_key_exists('is_current', $data)
            ? (bool) $data['is_current']
            : $experience->is_current;

        if ($effectiveIsCurrent) {
            $data['finished_at'] = null;
        }

        $oldLogoPath = $experience->company_logo;
        $storedLogoPath = null;

        try {
            DB::transaction(function () use ($request, $experience, $data, &$storedLogoPath): void {
                if ($request->hasFile('company_logo')) {
                    $storedLogoPath = $this->imageService->store($request->file('company_logo'), 'experiences');
                    $data['company_logo'] = $storedLogoPath;
                }

                $experience->update($data);
            });
        } catch (Throwable $exception) {
            $this->imageService->delete($storedLogoPath);

            throw $exception;
        }

        if ($request->hasFile('company_logo') && $oldLogoPath !== $experience->company_logo) {
            $this->imageService->delete($oldLogoPath);
        }

        return response()->json([
            'data' => ExperienceResource::make($experience->fresh()->load('media')),
            'message' => 'Experiencia actualizada correctamente.',
        ]);
    }

    public function destroy(Experience $experience): JsonResponse
    {
        $paths = $experience->media->pluck('path')->push($experience->company_logo)->filter()->all();

        DB::transaction(function () use ($experience): void {
            $experience->media()->delete();
            $experience->delete();
        });

        foreach ($paths as $path) {
            $this->imageService->delete((string) $path);
        }

        return response()->json([
            'message' => 'Experiencia eliminada correctamente.',
        ]);
    }
}
