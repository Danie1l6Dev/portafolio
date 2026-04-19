<?php

$allowedOrigins = array_values(array_unique(array_filter([
    'http://localhost:3000',
    env('FRONTEND_URL'),
])));

return [

    /*
    |--------------------------------------------------------------------------
    | CORS - Sanctum SPA (cookie/session)
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*',
        'login',
        'logout',
        'sanctum/csrf-cookie',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => $allowedOrigins,

    // Permite previews de Vercel si se usan subdominios *.vercel.app
    'allowed_origins_patterns' => [
        '#^https://.*\\.vercel\\.app$#',
    ],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
