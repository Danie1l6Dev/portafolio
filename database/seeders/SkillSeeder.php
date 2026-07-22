<?php

namespace Database\Seeders;

use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class SkillSeeder extends Seeder
{
    public function run(): void
    {
        // Formato: [name, group, icon, is_featured, sort_order]
        // Icono: prefijo "si:" = Simple Icons CDN (cdn.simpleicons.org/<slug>)
        //        null = sin icono
        $skills = [
            // ── Backend ───────────────────────────────────────
            ['PHP',             'Backend',       'si:php',          true,  10],
            ['Laravel',         'Backend',       'si:laravel',      true,  11],
            ['Livewire',        'Backend',       'si:livewire',     true,  12],
            ['Eloquent ORM',    'Backend',       'si:laravel',      false, 13],
            ['Laravel Sanctum', 'Backend',       'si:laravel',      false, 14],
            ['Python',           'Backend',       'si:python',       false, 15],
            ['Java',             'Backend',       'si:openjdk',      false, 16],

            // ── Frontend ──────────────────────────────────────
            ['Tailwind CSS',    'Frontend',      'si:tailwindcss',  true,  30],
            ['Blade',           'Frontend',      'si:laravel',      false, 31],
            ['JavaScript',      'Frontend',      'si:javascript',   false, 32],
            ['Alpine.js',       'Frontend',      'si:alpinedotjs',  false, 33],
            ['React',           'Frontend',      'si:react',        false, 34],

            // ── Base de datos ──────────────────────────────────
            ['MySQL',           'Base de datos', 'si:mysql',        true,  50],
            ['SQLite',          'Base de datos', 'si:sqlite',       false, 51],
            ['SQL Server',       'Base de datos', null,                    false, 52],
            ['MongoDB',          'Base de datos', 'si:mongodb',      false, 53],

            // ── DevOps ────────────────────────────────────────
            ['Docker',          'DevOps',        'si:docker',       false, 70],

            // ── Tools ─────────────────────────────────────────
            ['Git / GitHub',    'Tools',         'si:github',       true,  90],
            ['Vite',            'Tools',         'si:vite',         false, 91],
            ['PHPUnit / PEST',  'Tools',         'si:php',          false, 92],
            ['Figma',            'Tools',         'si:figma',        false, 93],
        ];

        // Elimina habilidades que ya no están en la lista
        $activeNames = array_column($skills, 0);
        Skill::whereNotIn('name', $activeNames)->delete();

        foreach ($skills as [$name, $group, $icon, $featured, $order]) {
            Skill::updateOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    'group' => $group,
                    'icon' => $icon,
                    'is_featured' => $featured,
                    'sort_order' => $order,
                ],
            );
        }
    }
}
