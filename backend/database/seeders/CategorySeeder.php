<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name'        => 'Web',
                'slug'        => 'web',
                'description' => 'Aplicaciones y sistemas web completos, tanto frontend como fullstack.',
                'color'       => '#3B82F6',
                'sort_order'  => 1,
            ],
            [
                'name'        => 'API / Backend',
                'slug'        => 'api-backend',
                'description' => 'Servicios, APIs REST y sistemas del lado del servidor.',
                'color'       => '#8B5CF6',
                'sort_order'  => 2,
            ],
            [
                'name'        => 'Mobile',
                'slug'        => 'mobile',
                'description' => 'Aplicaciones para dispositivos móviles iOS y Android.',
                'color'       => '#10B981',
                'sort_order'  => 3,
            ],
            [
                'name'        => 'CLI / Scripts',
                'slug'        => 'cli-scripts',
                'description' => 'Herramientas de línea de comandos, scripts de automatización y utilidades.',
                'color'       => '#6B7280',
                'sort_order'  => 4,
            ],
            [
                'name'        => 'IA / Machine Learning',
                'slug'        => 'ia-ml',
                'description' => 'Proyectos de inteligencia artificial, machine learning y análisis de datos.',
                'color'       => '#EC4899',
                'sort_order'  => 5,
            ],
            [
                'name'        => 'Juego',
                'slug'        => 'juego',
                'description' => 'Videojuegos y proyectos de desarrollo de juegos.',
                'color'       => '#F59E0B',
                'sort_order'  => 6,
            ],
            [
                'name'        => 'DevOps',
                'slug'        => 'devops',
                'description' => 'Infraestructura, CI/CD, contenedores y automatización de despliegues.',
                'color'       => '#0EA5E9',
                'sort_order'  => 7,
            ],
            [
                'name'        => 'Diseño / UI',
                'slug'        => 'diseno-ui',
                'description' => 'Interfaces de usuario, prototipos y proyectos de diseño visual.',
                'color'       => '#06B6D4',
                'sort_order'  => 8,
            ],
            [
                'name'        => 'Open Source',
                'slug'        => 'open-source',
                'description' => 'Contribuciones a proyectos de código abierto.',
                'color'       => '#F97316',
                'sort_order'  => 9,
            ],
            [
                'name'        => 'Portafolio',
                'slug'        => 'portafolio',
                'description' => 'Sitios y aplicaciones de portafolio personal.',
                'color'       => '#64748B',
                'sort_order'  => 10,
            ],
        ];

        foreach ($categories as $data) {
            Category::updateOrCreate(
                ['slug' => $data['slug']],
                $data,
            );
        }
    }
}
