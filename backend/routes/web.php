<?php

use App\Http\Controllers\Api\Auth\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login');

Route::get('/csrf-token', function (Request $request) {
    return response()->json([
        'csrf_token' => $request->session()->token(),
    ]);
})->name('csrf.token');

Route::middleware('auth')->post('/logout', [AuthController::class, 'logout'])
    ->name('logout');
