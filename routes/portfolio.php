<?php

use App\Http\Controllers\Api\Admin;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ExperienceController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\ProjectPageController;
use App\Http\Controllers\SitemapController;
use App\Livewire\Portfolio\ProjectBrowser;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:portfolio-public')->group(function (): void {
    Route::get('projects', [ProjectController::class, 'index'])->name('backend.projects.index');
    Route::get('projects/{project}', [ProjectController::class, 'show'])->name('backend.projects.show');
    Route::get('skills', [SkillController::class, 'index'])->name('backend.skills.index');
    Route::get('categories', [CategoryController::class, 'index'])->name('backend.categories.index');
    Route::get('experiences', [ExperienceController::class, 'index'])->name('backend.experiences.index');
});

Route::post('contact', [MessageController::class, 'store'])
    ->name('backend.contact')
    ->middleware('throttle:contact');

Route::get('proyectos', ProjectBrowser::class)->name('portfolio.projects.index');
Route::get('proyectos/{project:slug}', ProjectPageController::class)->name('portfolio.projects.show');

Route::redirect('habilidades', '/#habilidades')->name('portfolio.skills.redirect');
Route::redirect('experiencia', '/#experiencia')->name('portfolio.experience.redirect');
Route::redirect('contacto', '/#contacto')->name('portfolio.contact.redirect');

Route::get('sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('robots.txt', [SitemapController::class, 'robots'])->name('robots');

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['auth', 'verified', 'content-editor'])
    ->group(function (): void {
        Route::resource('categories', Admin\CategoryController::class)
            ->except(['create', 'edit']);

        Route::resource('projects', Admin\ProjectController::class)
            ->except(['create', 'edit']);

        Route::resource('skills', Admin\SkillController::class)
            ->except(['create', 'edit']);

        Route::resource('experiences', Admin\ExperienceController::class)
            ->except(['create', 'edit']);

        Route::prefix('messages')->name('messages.')->group(function (): void {
            Route::get('/', [Admin\MessageController::class, 'index'])->name('index');
            Route::get('/{message}', [Admin\MessageController::class, 'show'])->name('show');
            Route::patch('/{message}/read', [Admin\MessageController::class, 'markRead'])->name('mark-read');
            Route::post('/mark-all-read', [Admin\MessageController::class, 'markAllRead'])->name('mark-all-read');
            Route::delete('/{message}', [Admin\MessageController::class, 'destroy'])->name('destroy');
        });

        Route::post('projects/{project}/media', [Admin\MediaController::class, 'store'])->name('projects.media.store');
        Route::patch('projects/{project}/media/reorder', [Admin\MediaController::class, 'reorder'])->name('projects.media.reorder');
        Route::patch('projects/{project}/cover', [Admin\MediaController::class, 'setCover'])->name('projects.cover');
        Route::delete('media/{media}', [Admin\MediaController::class, 'destroy'])->name('media.destroy');
        Route::put('media/{media}', [Admin\MediaController::class, 'update'])->name('media.update');
    });
