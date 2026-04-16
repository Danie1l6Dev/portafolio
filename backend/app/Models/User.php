<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ];
    }

    // ── Roles ─────────────────────────────────────────────────

    /** Roles disponibles en el sistema. */
    public const ROLES = ['admin', 'editor'];

    /**
     * Comprueba si el usuario tiene rol de administrador.
     * Útil para gates, policies y middleware de acceso.
     *
     * @example
     *   if ($user->isAdmin()) { ... }
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isEditor(): bool
    {
        return in_array($this->role, ['admin', 'editor'], true);
    }

    /**
     * ── Escalabilidad: múltiples usuarios ────────────────────
     *
     * Para añadir soporte de múltiples usuarios con roles distintos:
     *
     * 1. Ejecutar la migración: add_role_to_users_table
     *    → Añade columna `role` ENUM('admin','editor') DEFAULT 'admin'
     *
     * 2. Al crear tokens de Sanctum, pasar las abilities del rol:
     *    $user->createToken('mobile-app', $user->tokenAbilities())
     *
     * 3. En el middleware/gate, verificar con $user->isAdmin()
     *    o tokenCan('admin') para proteger endpoints sensibles.
     *
     * 4. Los editores pueden gestionar contenido (CRUD completo)
     *    pero no pueden eliminar usuarios ni cambiar roles.
     */
    public function tokenAbilities(): array
    {
        return match ($this->role) {
            'admin'  => ['admin', 'editor', 'read'],
            'editor' => ['editor', 'read'],
            default  => ['read'],
        };
    }
}
