<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
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
    | GET  /api/v1/categories              → categorías con proyectos publicados
    | GET  /api/v1/experiences             → cronológica inversa
    | POST /api/v1/contact                 → enviar mensaje (throttle: contact)
    |
    | Los rate limiters están definidos en AppServiceProvider::configureRateLimiting()
    |
    */

    Route::middleware('throttle:api')->group(function () {
        Route::get('projects',           [ProjectController::class,   'index'])->name('projects.index');
        Route::get('projects/{project}', [ProjectController::class,   'show'])->name('projects.show');
        Route::get('skills',             [SkillController::class,     'index'])->name('skills.index');
        Route::get('categories',         [CategoryController::class,  'index'])->name('categories.index');
        Route::get('experiences',        [ExperienceController::class,'index'])->name('experiences.index');
    });

    Route::post('contact', [MessageController::class, 'store'])
        ->name('contact')
        ->middleware('throttle:contact');

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
            ->middleware('throttle:login');

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
    | ── Escalabilidad: múltiples usuarios ──────────────────────────────
    |
    | Para añadir roles/permisos granulares cuando el proyecto crezca:
    |
    | 1. Añade un middleware 'role:admin' usando $user->isAdmin()
    | 2. Usa Sanctum abilities: $user->currentAccessToken()->can('admin')
    | 3. Para permisos complejos, instala spatie/laravel-permission
    |
    | ── Escalabilidad: blog ─────────────────────────────────────────────
    |
    | Para añadir un blog, registra aquí:
    |   Route::apiResource('posts', Admin\PostController::class)
    | Y añade el endpoint público:
    |   Route::get('posts', [PostController::class, 'index'])
    |   Route::get('posts/{post:slug}', [PostController::class, 'show'])
    |
    | ── Escalabilidad: app móvil ────────────────────────────────────────
    |
    | La API ya es 100% consumible por una app móvil (React Native, Flutter).
    | El token Bearer es el mismo mecanismo de auth.
    | Para tokens de larga duración, configura SANCTUM_EXPIRATION en .env.
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
