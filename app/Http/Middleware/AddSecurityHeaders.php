<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Vite;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    /**
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        if (app()->isProduction() && $request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        $this->applyContentSecurityPolicy($response);

        return $response;
    }

    private function applyContentSecurityPolicy(Response $response): void
    {
        // Con el servidor de Vite en marcha los assets vienen de otro origen.
        if (! config('security.csp.enabled') || Vite::isRunningHot()) {
            return;
        }

        /** @var array<string, list<string>> $directives */
        $directives = config('security.csp.directives');

        $policy = implode('; ', array_map(
            fn (string $directive, array $sources): string => $directive.' '.implode(' ', $sources),
            array_keys($directives),
            $directives,
        ));

        $header = config('security.csp.report_only')
            ? 'Content-Security-Policy-Report-Only'
            : 'Content-Security-Policy';

        $response->headers->set($header, $policy);
    }
}
