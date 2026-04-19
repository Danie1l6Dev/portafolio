<?php

$extraOrigins = array_filter(array_map(
    static fn (string $origin) => trim($origin),
    explode(',', (string) env('CORS_ALLOWED_ORIGINS', '')),
));

$allowedOrigins = array_values(array_unique(array_filter(array_merge([
    'http://localhost:3000',
    env('FRONTEND_URL'),
], $extraOrigins))));

$originPatterns = array_values(array_filter(array_map(
    static fn (string $pattern) => trim($pattern),
    explode(',', (string) env('CORS_ALLOWED_ORIGINS_PATTERNS', '#^https://.*\\.vercel\\.app$#')),
)));

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

    'allowed_origins_patterns' => $originPatterns,

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 86400,

    'supports_credentials' => true,

];
