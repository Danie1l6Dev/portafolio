<?php

return [

    /*
    |--------------------------------------------------------------------------
    | CORS — Cross-Origin Resource Sharing
    |--------------------------------------------------------------------------
    |
    | El frontend Next.js corre en un origen diferente (localhost:3000 en dev,
    | dominio propio en producción). Se configura aquí para que Laravel permita
    | las peticiones con el header Authorization: Bearer {token}.
    |
    | supports_credentials = false porque usamos tokens Bearer, no cookies.
    |
    */

    'paths' => ['api/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://localhost:3000',
        'https://portafoliodaniel-two.vercel.app',
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => [
        'Content-Type',
        'Authorization',
        'Accept',
        'X-Requested-With',
    ],

    'exposed_headers' => [],

    'max_age' => 86400, // 24h cache preflight

    'supports_credentials' => false,

];
