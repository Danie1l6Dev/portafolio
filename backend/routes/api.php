<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API pública — sin autenticación
|--------------------------------------------------------------------------
|
| Prefijo: /api/v1
|
| GET  /api/v1/projects            → lista paginada (solo publicados)
| GET  /api/v1/projects/{project}  → detalle con skills y media
| GET  /api/v1/skills              → lista completa con grupos
| GET  /api/v1/experiences         → cronológica inversa
|
*/

Route::prefix('v1')->name('api.')->group(function () {

    // ── Proyectos públicos ────────────────────────────────────
    Route::get('projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    Route::get('projects/{project}', [ProjectController::class, 'show'])
        ->name('projects.show');

    // ── Habilidades públicas ──────────────────────────────────
    Route::get('skills', [SkillController::class, 'index'])
        ->name('skills.index');

    // ── Experiencias públicas ─────────────────────────────────
    Route::get('experiences', [ExperienceController::class, 'index'])
        ->name('experiences.index');

    /*
    |----------------------------------------------------------------------
    | Autenticación — Sanctum / Bearer token
    |----------------------------------------------------------------------
    |
    | POST /api/v1/auth/login    → devuelve { data: { user, token } }
    | POST /api/v1/auth/logout   → revoca token actual  [AUTH requerida]
    | GET  /api/v1/auth/me       → datos del usuario    [AUTH requerida]
    |
    */

    Route::prefix('auth')->name('auth.')->group(function () {

        // Pública — throttle: máx 5 intentos / minuto por IP
        Route::post('login', [AuthController::class, 'login'])
            ->name('login')
            ->middleware('throttle:5,1');

        // Protegidas — requieren Bearer token válido
        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me', [AuthController::class, 'me'])->name('me');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Panel Admin — rutas protegidas (Fase 5+)
    |----------------------------------------------------------------------
    |
    | Todas las rutas bajo /api/v1/admin requieren token de administrador.
    | Los controladores CRUD se implementarán en la siguiente fase.
    |
    */

    Route::prefix('admin')->name('admin.')->middleware('auth:sanctum')->group(function () {

        // Proyectos
        Route::apiResource('projects', ProjectController::class)
            ->only(['store', 'update', 'destroy'])
            ->names('projects');

        // Habilidades
        Route::apiResource('skills', SkillController::class)
            ->only(['store', 'update', 'destroy'])
            ->names('skills');

        // Experiencias
        Route::apiResource('experiences', ExperienceController::class)
            ->only(['store', 'update', 'destroy'])
            ->names('experiences');
    });
});
