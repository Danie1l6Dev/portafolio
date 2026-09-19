<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class ContactEmailController extends Controller
{
    /**
     * GET /contacto/correo
     *
     * Entrega el correo público solo cuando el visitante pulsa "Copiar correo",
     * para que no viaje en el HTML que rastrean los recolectores de direcciones.
     */
    public function __invoke(): JsonResponse
    {
        return response()
            ->json(['email' => config('portfolio.email')])
            ->header('Cache-Control', 'no-store')
            ->header('X-Robots-Tag', 'noindex, nofollow');
    }
}
