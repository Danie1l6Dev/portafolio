<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

Route::prefix('admin')
    ->name('panel.')
    ->middleware(['auth', 'verified', 'content-editor'])
    ->group(function (): void {
        Route::view('/', 'admin.page', [
            'livewireComponent' => 'admin.dashboard',
            'title' => 'Panel editorial',
        ])->name('dashboard');

        Route::view('proyectos', 'admin.page', [
            'livewireComponent' => 'admin.project-manager',
            'title' => 'Proyectos',
        ])->name('projects');

        Route::view('categorias', 'admin.page', [
            'livewireComponent' => 'admin.category-manager',
            'title' => 'Categorías',
        ])->name('categories');

        Route::view('habilidades', 'admin.page', [
            'livewireComponent' => 'admin.skill-manager',
            'title' => 'Habilidades',
        ])->name('skills');

        Route::view('experiencias', 'admin.page', [
            'livewireComponent' => 'admin.experience-manager',
            'title' => 'Experiencias',
        ])->name('experiences');

        Route::view('mensajes', 'admin.page', [
            'livewireComponent' => 'admin.message-inbox',
            'title' => 'Mensajes',
        ])->name('messages');
    });

require __DIR__.'/settings.php';
require __DIR__.'/portfolio.php';
