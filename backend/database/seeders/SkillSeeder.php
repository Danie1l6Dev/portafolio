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
        $skills = [
            // ── Backend ───────────────────────────────────────
            ['PHP',               'Backend',       5, '🐘', true,  10],
            ['Laravel',           'Backend',       5, '🔴', true,  11],
            ['Livewire',          'Backend',       5, '⚡', true,  12],
            ['Laravel Sanctum',   'Backend',       3, null,  false, 13],
            ['Laravel Fortify',   'Backend',       3, null,  false, 14],
            ['Eloquent ORM',      'Backend',       5, null,  false, 15],
            ['simple-qrcode',     'Backend',       4, null,  false, 16],
            ['Maatwebsite Excel', 'Backend',       4, null,  false, 17],
            ['PHPSpreadsheet',    'Backend',       3, null,  false, 18],
            ['DomPDF',            'Backend',       3, null,  false, 19],
            ['FPDI/TFPDF',        'Backend',       3, null,  false, 20],

            // ── Frontend ──────────────────────────────────────
            ['Tailwind CSS',      'Frontend',      5, '🎨', true,  30],
            ['Blade',             'Frontend',      4, null,  false, 31],
            ['Alpine.js',         'Frontend',      3, null,  false, 32],
            ['JavaScript',        'Frontend',      4, '📜', false, 33],
            ['React',             'Frontend',      3, '⚛️', false, 34],
            ['Axios',             'Frontend',      3, null,  false, 35],
            ['Recharts',          'Frontend',      3, null,  false, 36],

            // ── Base de datos ──────────────────────────────────
            ['MySQL',             'Base de datos', 5, '🗄️', true,  50],
            ['SQLite',            'Base de datos', 4, null,  false, 51],

            // ── DevOps ────────────────────────────────────────
            ['Docker',            'DevOps',        3, '🐳', false, 70],
            ['docker-compose',    'DevOps',        2, null,  false, 71],
            ['Nginx',             'DevOps',        2, null,  false, 72],
            ['Render',            'DevOps',        3, null,  false, 73],

            // ── Tools ─────────────────────────────────────────
            ['Git / GitHub',      'Tools',         4, '🐙', true,  90],
            ['Vite',              'Tools',         4, null,  false, 91],
            ['NPM',               'Tools',         3, null,  false, 92],
            ['PHPUnit',           'Tools',         3, null,  false, 93],
            ['PEST PHP',          'Tools',         3, null,  false, 94],
            ['Laravel Pint',      'Tools',         2, null,  false, 95],
        ];

        foreach ($skills as $i => [$name, $group, $level, $icon, $featured, $order]) {
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
