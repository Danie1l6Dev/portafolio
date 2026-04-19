<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Usuario autenticado (Sanctum SPA session)
|--------------------------------------------------------------------------
|
| GET /api/user
|
*/
Route::middleware('auth:sanctum')->get('user', [AuthController::class, 'me'])
    ->name('api.user');

Route::get('csrf-token', function (Request $request) {
    return response()->json([
        'csrf_token' => $request->session()->token(),
    ]);
})->name('api.csrf-token');

Route::prefix('v1')->name('api.')->group(function () {

    /*
    |----------------------------------------------------------------------
    | API publica - sin autenticacion
    |----------------------------------------------------------------------
    */
    Route::middleware('throttle:api')->group(function () {
        Route::get('projects',           [ProjectController::class, 'index'])->name('projects.index');
        Route::get('projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        Route::get('skills',             [SkillController::class, 'index'])->name('skills.index');
        Route::get('categories',         [CategoryController::class, 'index'])->name('categories.index');
        Route::get('experiences',        [ExperienceController::class, 'index'])->name('experiences.index');
    });

    Route::post('contact', [MessageController::class, 'store'])
        ->name('contact')
        ->middleware('throttle:contact');

    /*
    |----------------------------------------------------------------------
    | Panel Admin - protegido por auth:sanctum (sesion/cookie)
    |----------------------------------------------------------------------
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

        // Mensajes de contacto - solo lectura + eliminacion
        Route::prefix('messages')->name('messages.')->group(function () {
            Route::get('/',               [Admin\MessageController::class, 'index'])->name('index');
            Route::get('/{message}',      [Admin\MessageController::class, 'show'])->name('show');
            Route::patch('/{message}/read', [Admin\MessageController::class, 'markRead'])->name('mark-read');
            Route::post('/mark-all-read', [Admin\MessageController::class, 'markAllRead'])->name('mark-all-read');
            Route::delete('/{message}',   [Admin\MessageController::class, 'destroy'])->name('destroy');
        });
    });
});
