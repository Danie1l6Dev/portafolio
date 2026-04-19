<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        // Formato: [name, group, level (1-5), icon, is_featured, sort_order]
        // Nivel: 1=básico, 2=familiar, 3=competente, 4=avanzado, 5=experto
        // Icono: prefijo "si:" = Simple Icons CDN (cdn.simpleicons.org/<slug>)
        //        null = sin icono
        $skills = [
            // ── Backend ───────────────────────────────────────
            ['PHP',             'Backend',       5, 'si:php',          true,  10],
            ['Laravel',         'Backend',       5, 'si:laravel',      true,  11],
            ['Livewire',        'Backend',       3, 'si:livewire',     true,  12],
            ['Eloquent ORM',    'Backend',       3, 'si:laravel',      false, 13],
            ['Laravel Sanctum', 'Backend',       3, 'si:laravel',      false, 14],

            // ── Frontend ──────────────────────────────────────
            ['Tailwind CSS',    'Frontend',      4, 'si:tailwindcss',  true,  30],
            ['Blade',           'Frontend',      4, 'si:laravel',      false, 31],
            ['JavaScript',      'Frontend',      3, 'si:javascript',   false, 32],
            ['Alpine.js',       'Frontend',      3, 'si:alpinedotjs',  false, 33],
            ['React',           'Frontend',      2, 'si:react',        false, 34],

            // ── Base de datos ──────────────────────────────────
            ['MySQL',           'Base de datos', 4, 'si:mysql',        true,  50],
            ['SQLite',          'Base de datos', 2, 'si:sqlite',       false, 51],

            // ── DevOps ────────────────────────────────────────
            ['Docker',          'DevOps',        3, 'si:docker',       false, 70],

            // ── Tools ─────────────────────────────────────────
            ['Git / GitHub',    'Tools',         4, 'si:github',       true,  90],
            ['Vite',            'Tools',         3, 'si:vite',         false, 91],
            ['PHPUnit / PEST',  'Tools',         3, 'si:php',      false, 92],
        ];

        // Elimina habilidades que ya no están en la lista
        $activeNames = array_column($skills, 0);
        Skill::whereNotIn('name', $activeNames)->delete();

        foreach ($skills as [$name, $group, $level, $icon, $featured, $order]) {
            Skill::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name'        => $name,
                    'slug'        => Str::slug($name),
                    'group'       => $group,
                    'level'       => $level,
                    'icon'        => $icon,
                    'is_featured' => $featured,
                    'sort_order'  => $order,
                ],
            );
        }
    }
}
