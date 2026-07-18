<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ExperienceResource;
use App\Models\Experience;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ExperienceController extends Controller
{
    /**
     * GET /api/experiences
     *
     * Lista todas las experiencias laborales en orden cronológico inverso
     * (más reciente primero). Incluye media adjunta (logos, etc.).
     */
    public function index(): AnonymousResourceCollection
    {
        $experiences = Experience::query()
            ->with('media')
            ->ordered()
            ->get();

        return ExperienceResource::collection($experiences);
    }
}
