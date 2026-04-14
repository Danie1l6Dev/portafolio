<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Crea el usuario administrador si no existe.
     *
     * Credenciales por defecto (cambiar antes de producción):
     *   Email:    admin@portafolio.test
     *   Password: password
     *
     * Para cambiarlas, definir en .env:
     *   ADMIN_EMAIL=tu@email.com
     *   ADMIN_PASSWORD=tu_password_seguro
     *   ADMIN_NAME="Tu Nombre"
     */
    public function run(): void
    {
        $email = env('ADMIN_EMAIL', 'admin@portafolio.test');

        User::firstOrCreate(
            ['email' => $email],
            [
                'name'     => env('ADMIN_NAME', 'Administrador'),
                'email'    => $email,
                'password' => Hash::make(env('ADMIN_PASSWORD', 'password')),
            ]
        );

        $this->command->info("Usuario admin listo: {$email}");
    }
}
