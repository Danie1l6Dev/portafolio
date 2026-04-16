<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\Admin;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | API pública — sin autenticación
    |----------------------------------------------------------------------
    |
    | GET  /api/v1/projects                → lista paginada (publicados)
    | GET  /api/v1/projects/{project}      → detalle con skills y media
    | GET  /api/v1/skills                  → lista + meta grupos
    | GET  /api/v1/experiences             → cronológica inversa
    |
    */

    Route::get('projects',          [ProjectController::class,   'index'])->name('projects.index');
    Route::get('projects/{project}',[ProjectController::class,   'show'])->name('projects.show');
    Route::get('skills',            [SkillController::class,     'index'])->name('skills.index');
    Route::get('experiences',       [ExperienceController::class,'index'])->name('experiences.index');
    Route::post('contact',          [MessageController::class,   'store'])->name('contact')->middleware('throttle:5,1');

    /*
    |----------------------------------------------------------------------
    | Autenticación — Bearer token (Sanctum)
    |----------------------------------------------------------------------
    |
    | POST /api/v1/auth/login    → { data: { user, token } }
    | POST /api/v1/auth/logout   → revoca token         [auth requerida]
    | GET  /api/v1/auth/me       → usuario autenticado  [auth requerida]
    |
    */

    Route::prefix('auth')->name('auth.')->group(function () {

        Route::post('login', [AuthController::class, 'login'])
            ->name('login')
            ->middleware('throttle:5,1');

        Route::middleware('auth:sanctum')->group(function () {
            Route::post('logout', [AuthController::class, 'logout'])->name('logout');
            Route::get('me',      [AuthController::class, 'me'])->name('me');
        });
    });

    /*
    |----------------------------------------------------------------------
    | Panel Admin — protegido por auth:sanctum
    |----------------------------------------------------------------------
    |
    | Todas las rutas bajo /api/v1/admin requieren: Authorization: Bearer {token}
    |
    | Categorías:
    |   GET    /api/v1/admin/categories          → lista + projects_count
    |   POST   /api/v1/admin/categories          → crear
    |   GET    /api/v1/admin/categories/{id}     → detalle
    |   PUT    /api/v1/admin/categories/{id}     → actualizar
    |   DELETE /api/v1/admin/categories/{id}     → eliminar (falla si tiene proyectos)
    |
    | Proyectos:
    |   GET    /api/v1/admin/projects            → lista todos (draft/pub/archived)
    |   POST   /api/v1/admin/projects            → crear + imagen + skill_ids[]
    |   GET    /api/v1/admin/projects/{id}       → detalle completo
    |   PUT    /api/v1/admin/projects/{id}       → actualizar + nueva imagen
    |   DELETE /api/v1/admin/projects/{id}       → eliminar + borrar archivos
    |
    | Habilidades:
    |   GET    /api/v1/admin/skills              → lista + projects_count
    |   POST   /api/v1/admin/skills              → crear
    |   GET    /api/v1/admin/skills/{id}         → detalle
    |   PUT    /api/v1/admin/skills/{id}         → actualizar
    |   DELETE /api/v1/admin/skills/{id}         → eliminar
    |
    | Experiencias:
    |   GET    /api/v1/admin/experiences         → lista cronológica
    |   POST   /api/v1/admin/experiences         → crear + logo
    |   GET    /api/v1/admin/experiences/{id}    → detalle
    |   PUT    /api/v1/admin/experiences/{id}    → actualizar + nuevo logo
    |   DELETE /api/v1/admin/experiences/{id}    → eliminar + borrar archivos
    |
    */

    Route::prefix('admin')->name('admin.')->middleware('auth:sanctum')->group(function () {

        Route::apiResource('categories', Admin\CategoryController::class)
            ->names('categories');

        Route::apiResource('projects', Admin\ProjectController::class)
            ->names('projects');

        Route::apiResource('skills', Admin\SkillController::class)
            ->names('skills');

        Route::apiResource('experiences', Admin\ExperienceController::class)
            ->names('experiences');
    });
});
