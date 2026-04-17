<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\Skill;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        // ── Proyecto 1: Asistencia Uniguajira ────────────────
        $this->upsertProject(
            title:       'Asistencia Uniguajira',
            category:    'web',
            summary:     'Sistema web para la gestión y control de asistencias en la Universidad de La Guajira, con registro público vía códigos QR y panel administrativo completo.',
            description: <<<TEXT
            Resuelve el problema de llevar el control de asistencia a eventos universitarios (charlas, capacitaciones, actos) centralizando el registro de participantes, dependencias, programas y estamentos en una sola plataforma. Los organizadores crean eventos desde un panel administrativo y los asistentes se registran públicamente escaneando un código QR, sin necesidad de tener cuenta.

            Incluye estadísticas y gráficos en tiempo real, generación de reportes PDF de asistencia, importación/exportación masiva de datos vía Excel, notificaciones por correo y un sistema de roles (admin vs. usuarios de dependencia). Lo especial es la combinación de un flujo público por QR para los participantes con un backoffice rico en Livewire + React para los administradores, todo localizado al español.
            TEXT,
            demo_url:    'https://asistencia-uniguajira.onrender.com/',
            repo_url:    'https://github.com/KevinHGitCode/Asistencia-Uniguajira',
            status:      'published',
            is_featured: true,
            sort_order:  1,
            started_at:  '2025-08-01',
            finished_at: null,
            skills: [
                'PHP', 'Laravel', 'Livewire', 'Blade', 'Tailwind CSS',
                'Alpine.js', 'React', 'JavaScript', 'Vite',
                'SQLite', 'MySQL', 'Laravel Sanctum', 'Eloquent ORM',
                'Docker', 'Git / GitHub', 'PHPUnit / PEST',
            ],
        );

        // ── Proyecto 2: Inventario Uniguajira ────────────────
        $this->upsertProject(
            title:       'Inventario Uniguajira',
            category:    'web',
            summary:     'Sistema web de gestión de inventario institucional para la Universidad de la Guajira. Permite registrar, organizar y hacer seguimiento de bienes y activos distribuidos en grupos e inventarios.',
            description: <<<TEXT
            Resuelve la necesidad de la Universidad de la Guajira de controlar su inventario de activos físicos de forma centralizada y con trazabilidad completa. La aplicación organiza los bienes en grupos e inventarios, soporta dos tipos de activos (por cantidad y por número de serie), y registra automáticamente toda actividad del sistema (creación, edición, eliminación, login/logout).

            Cuenta con importación masiva vía Excel, exportación de reportes en PDF y CSV, gestión de usuarios con dos roles (administrador y consultor), y autenticación con soporte de 2FA. Está desplegada en contenedores Docker y tiene una suite de pruebas automatizadas con más de 93 tests cubriendo CRUD, validaciones y control de acceso por rol.
            TEXT,
            demo_url:    null,
            repo_url:    'https://github.com/KevinHGitCode/Inventario-Uniguajira-Laravel12',
            status:      'published',
            is_featured: true,
            sort_order:  2,
            started_at:  '2025-11-01',
            finished_at: null,
            skills: [
                'PHP', 'Laravel', 'Livewire', 'Blade', 'Tailwind CSS',
                'JavaScript', 'Vite',
                'MySQL', 'Eloquent ORM',
                'Docker', 'Git / GitHub', 'PHPUnit / PEST',
            ],
        );
    }

    // ── Helper ────────────────────────────────────────────────

    private function upsertProject(
        string  $title,
        string  $category,
        string  $summary,
        string  $description,
        ?string $demo_url,
        ?string $repo_url,
        string  $status,
        bool    $is_featured,
        int     $sort_order,
        string  $started_at,
        ?string $finished_at,
        array   $skills,
    ): void {
        $categoryModel = Category::where('slug', $category)->first();
        $slug          = Str::slug($title);

        /** @var Project $project */
        $project = Project::updateOrCreate(
            ['slug' => $slug],
            [
                'category_id' => $categoryModel?->id,
                'title'       => $title,
                'slug'        => $slug,
                'summary'     => trim($summary),
                'description' => trim($description),
                'demo_url'    => $demo_url,
                'repo_url'    => $repo_url,
                'status'      => $status,
                'is_featured' => $is_featured,
                'sort_order'  => $sort_order,
                'started_at'  => $started_at,
                'finished_at' => $finished_at,
            ],
        );

        // Asocia habilidades (busca por nombre exacto)
        $skillIds = Skill::whereIn('name', $skills)->pluck('id');
        $project->skills()->sync($skillIds);
    }
}
