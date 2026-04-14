<?php

use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API pública del portafolio — sin autenticación
|--------------------------------------------------------------------------
|
| Prefijo base: /api/v1
|
| Proyectos:
|   GET /api/v1/projects              → lista paginada (publicados)
|   GET /api/v1/projects/{project}    → detalle con skills y media
|
| Habilidades:
|   GET /api/v1/skills                → lista completa, meta con grupos
|
| Experiencias:
|   GET /api/v1/experiences           → lista cronológica inversa
|
*/

Route::prefix('v1')->name('api.')->group(function () {

    // ── Proyectos ─────────────────────────────────────────────
    Route::get('projects', [ProjectController::class, 'index'])
        ->name('projects.index');

    Route::get('projects/{project}', [ProjectController::class, 'show'])
        ->name('projects.show');

    // ── Habilidades ───────────────────────────────────────────
    Route::get('skills', [SkillController::class, 'index'])
        ->name('skills.index');

    // ── Experiencias ──────────────────────────────────────────
    Route::get('experiences', [ExperienceController::class, 'index'])
        ->name('experiences.index');
});

/*
|--------------------------------------------------------------------------
| Ruta protegida de usuario — Sanctum (se ampliará en Fase auth)
|--------------------------------------------------------------------------
*/
Route::get('/user', function () {
    // Se configurará cuando se implemente autenticación
})->middleware('auth:sanctum');
