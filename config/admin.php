<?php

return [
    'name' => env('ADMIN_NAME', 'Administrador'),
    'email' => env('ADMIN_EMAIL', 'admin@portafolio.test'),
    'password' => env('ADMIN_PASSWORD', 'password'),

    'galleries' => [
        'projects' => [
            'max_items' => 8,
            'max_file_kilobytes' => 2048,
        ],
        'achievements' => [
            'max_items' => 12,
            'max_file_kilobytes' => 3072,
        ],
    ],
];
