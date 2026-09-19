<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Content-Security-Policy
    |--------------------------------------------------------------------------
    |
    | Por defecto se envía en modo solo-reporte: el navegador no bloquea nada,
    | solo muestra en la consola lo que bloquearía. Cuando la consola esté limpia
    | en el sitio público y en el panel, define CSP_REPORT_ONLY=false para aplicarla.
    |
    | 'unsafe-eval' lo exige Alpine (incluido en Livewire/Flux) y 'unsafe-inline'
    | los scripts y estilos en línea del layout. No se aplica con el servidor de
    | Vite en marcha (public/hot).
    |
    */
    'csp' => [
        'enabled' => (bool) env('CSP_ENABLED', true),
        'report_only' => (bool) env('CSP_REPORT_ONLY', true),

        'directives' => [
            'default-src' => ["'self'"],
            'script-src' => ["'self'", "'unsafe-inline'", "'unsafe-eval'"],
            'style-src' => ["'self'", "'unsafe-inline'"],
            'img-src' => ["'self'", 'data:', 'blob:', 'https://cdn.simpleicons.org'],
            'font-src' => ["'self'", 'data:'],
            'connect-src' => ["'self'"],
            'object-src' => ["'none'"],
            'base-uri' => ["'self'"],
            'form-action' => ["'self'"],
            'frame-ancestors' => ["'self'"],
        ],
    ],
];
