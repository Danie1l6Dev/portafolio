<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * POST /api/v1/auth/login
     *
     * Verifica las credenciales del administrador y devuelve un token Bearer.
     * El token debe almacenarse en el cliente (Next.js) y enviarse en cada
     * petición protegida como: Authorization: Bearer {token}
     *
     * Respuesta 200:
     * {
     *   "data": {
     *     "user":  { id, name, email },
     *     "token": "1|abc123..."
     *   }
     * }
     *
     * Respuesta 401:
     * { "message": "Credenciales incorrectas." }
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Credenciales incorrectas.',
            ], 401);
        }

        // Revocar tokens anteriores del mismo dispositivo/nombre para no acumular
        $user->tokens()->where('name', 'admin-panel')->delete();

        $token = $user->createToken('admin-panel', ['admin'])->plainTextToken;

        return response()->json([
            'data' => [
                'user'  => UserResource::make($user),
                'token' => $token,
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     *
     * Revoca el token actual. Requiere: Authorization: Bearer {token}
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'message' => 'Sesión cerrada correctamente.',
        ]);
    }

    /**
     * GET /api/v1/auth/me
     *
     * Devuelve los datos del usuario autenticado.
     * Útil para que Next.js verifique si el token sigue siendo válido.
     */
    public function me(Request $request): JsonResponse
    {
        return response()->json([
            'data' => UserResource::make($request->user()),
        ]);
    }
}
