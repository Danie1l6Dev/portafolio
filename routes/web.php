<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Admin\Projects\Index;

Route::view('/', 'welcome');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        Route::get('/projects', Index::class)
            ->name('projects.index');
    });

require __DIR__ . '/auth.php';
