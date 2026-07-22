<?php

return [
    'name' => env('PORTFOLIO_NAME', 'Daniel Sierra'),
    'initials' => env('PORTFOLIO_INITIALS', 'DS'),
    'email' => env('PORTFOLIO_EMAIL', 'danielsierra103@gmail.com'),
    'role' => env('PORTFOLIO_ROLE', 'Desarrollador de software · Ingeniería de Sistemas en formación'),
    'description' => env(
        'PORTFOLIO_DESCRIPTION',
        'Desarrollador de software enfocado en aplicaciones web, automatización de procesos y gestión de datos.',
    ),
    'hero_title' => 'Transformo procesos operativos en',
    'hero_highlight' => 'aplicaciones web claras.',
    'intro' => 'Desarrollo aplicaciones con Laravel para digitalizar registros, inventarios y flujos administrativos mediante interfaces claras, reportes y datos organizados.',
    'about' => 'Soy estudiante de Ingeniería de Sistemas en la Universidad de La Guajira y desarrollador de software con experiencia construyendo soluciones para procesos institucionales. Desde 2023 también acompaño a estudiantes como tutor académico en lógica de programación y resolución de problemas.',
    'availability' => 'Disponible para proyectos freelance, colaboraciones y nuevas oportunidades.',
    'response_time' => 'Normalmente respondo en menos de 48 horas.',
    'education' => [
        'program' => 'Ingeniería de Sistemas',
        'institution' => 'Universidad de La Guajira',
        'period' => '2022 — Actualidad',
        'location' => 'Maicao, La Guajira',
    ],
    'resume' => [
        'path' => 'documents/hoja-de-vida-daniel-sierra.pdf',
        'download_name' => 'Hoja de vida - Daniel Sierra.pdf',
    ],
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
