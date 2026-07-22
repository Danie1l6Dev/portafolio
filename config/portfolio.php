<?php

return [
    'name' => env('PORTFOLIO_NAME', 'Daniel Sierra'),
    'initials' => env('PORTFOLIO_INITIALS', 'DS'),
    'email' => env('PORTFOLIO_EMAIL', 'danielsierra103@gmail.com'),
    'role' => env('PORTFOLIO_ROLE', 'Desarrollador de software'),
    'description' => env(
        'PORTFOLIO_DESCRIPTION',
        'Desarrollador de software. Proyectos, habilidades y experiencia profesional.',
    ),
    'hero_title' => 'Transformo procesos operativos en',
    'hero_highlight' => 'aplicaciones web claras.',
    'intro' => 'Diseño y desarrollo sistemas con Laravel que reúnen datos, usuarios y tareas en una experiencia fácil de usar y mantener.',
    'about' => 'Me especializo en transformar necesidades operativas en aplicaciones web claras y confiables. Trabajo principalmente con PHP, Laravel, Livewire y Tailwind CSS, cuidando tanto la arquitectura como la experiencia de quienes usan el producto.',
    'availability' => 'Disponible para proyectos freelance, colaboraciones y nuevas oportunidades.',
    'response_time' => 'Normalmente respondo en menos de 48 horas.',
    'services' => [
        [
            'label' => 'Centralizar',
            'title' => 'Sistemas de gestión',
            'description' => 'Centralizo usuarios, roles, datos y procesos en paneles claros y fáciles de operar.',
        ],
        [
            'label' => 'Digitalizar',
            'title' => 'Procesos operativos',
            'description' => 'Creo flujos para registros, inventarios, trazabilidad, importaciones y reportes.',
        ],
        [
            'label' => 'Construir',
            'title' => 'Aplicaciones Laravel completas',
            'description' => 'Integro interfaz pública, panel administrativo, base de datos, validaciones y pruebas.',
        ],
    ],
    'delivery_steps' => ['Necesidad', 'Arquitectura', 'Desarrollo', 'Validación'],
    'socials' => [
        [
            'name' => 'GitHub',
            'url' => env('PORTFOLIO_GITHUB_URL', 'https://github.com/Danie1l6Dev'),
            'icon' => 'github',
        ],
        [
            'name' => 'LinkedIn',
            'url' => env('PORTFOLIO_LINKEDIN_URL', 'https://www.linkedin.com/in/daniel-sierra-44262a3b6'),
            'icon' => 'linkedin',
        ],
        [
            'name' => 'Instagram',
            'url' => env('PORTFOLIO_INSTAGRAM_URL', 'https://instagram.com/danie1l6'),
            'icon' => 'instagram',
        ],
    ],
];
