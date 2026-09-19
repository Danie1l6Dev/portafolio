<?php

return [
    'name' => env('ADMIN_NAME', 'Administrador'),
    'email' => env('ADMIN_EMAIL', 'admin@portafolio.test'),
    'password' => env('ADMIN_PASSWORD', 'password'),

    'images' => [
        'max_file_kilobytes' => 10_240,
        'detail_max_width' => 1920,
        'preview_max_width' => 960,
        'detail_webp_quality' => 82,
        'preview_webp_quality' => 76,
    ],

    'resume' => [
        'max_file_kilobytes' => 5_120,
    ],

    'galleries' => [
        'projects' => [
            'max_items' => 8,
            'max_file_kilobytes' => 10_240,
        ],
        'achievements' => [
            'max_items' => 12,
            'max_file_kilobytes' => 10_240,
        ],
    ],
];
