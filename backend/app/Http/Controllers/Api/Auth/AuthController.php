<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * POST /login
     *
     * Inicia sesion usando la sesion web de Laravel (cookie HttpOnly).
     * Requiere haber solicitado previamente /sanctum/csrf-cookie desde el SPA.
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        if (! Auth::attempt($credentials, $request->boolean('remember'))) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        $request->session()->regenerate();

        return response()->json([
            'data' => [
                'user' => UserResource::make($request->user()),
            ],
        ]);
    }

    /**
     * POST /logout
     *
     * Cierra la sesion actual y regenera la cookie/CSRF token.
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return response()->json([
            'message' => 'Sesion cerrada correctamente.',
        ]);
    }

    /**
     * GET /api/user
     *
     * Devuelve los datos del usuario autenticado por sesion Sanctum.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => UserResource::make($request->user()),
        ]);
    }
}
