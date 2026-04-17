<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminSeeder::class,    // Usuario admin (debe ir primero)
            CategorySeeder::class, // Categorías (requeridas por ProjectSeeder)
            SkillSeeder::class,    // Habilidades (requeridas por ProjectSeeder)
            ProjectSeeder::class,  // Proyectos + relación con skills
        ]);
    }
}
