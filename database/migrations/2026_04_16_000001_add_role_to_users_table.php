<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Añade soporte de roles al modelo User.
 *
 * Roles disponibles:
 *   - admin  → acceso total (gestión de usuarios, configuración)
 *   - editor → acceso a contenido (proyectos, skills, experiencias) sin gestión de usuarios
 *
 * Para futuras ampliaciones (más roles, permisos granulares):
 *   - Considera usar spatie/laravel-permission
 *   - O añadir una tabla `roles` y una `role_user` pivot
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->enum('role', ['admin', 'editor'])
                ->default('admin')
                ->after('email')
                ->comment('admin = acceso total; editor = solo contenido');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
