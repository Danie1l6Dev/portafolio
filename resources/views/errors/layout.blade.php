<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex,nofollow">
        <meta name="theme-color" content="#07111f">
        <title>@yield('code') — {{ config('portfolio.name', config('app.name')) }}</title>
        <link rel="icon" href="/favicon.svg?v=2" type="image/svg+xml">
        <style>
            :root { color-scheme: dark light; }
            * { box-sizing: border-box; }
            body {
                margin: 0;
                min-height: 100vh;
                display: grid;
                place-items: center;
                padding: 1.5rem;
                font-family: ui-sans-serif, system-ui, -apple-system, 'Segoe UI', sans-serif;
                background: #07111f;
                color: #e2e8f0;
            }
            main { max-width: 32rem; text-align: center; }
            .code { margin: 0; font-size: clamp(4rem, 18vw, 7rem); font-weight: 800; line-height: 1; color: #38bdf8; }
            h1 { margin: 1rem 0 .5rem; font-size: 1.5rem; }
            p { margin: 0 0 2rem; line-height: 1.6; color: #94a3b8; }
            a {
                display: inline-block;
                padding: .75rem 1.5rem;
                border-radius: .5rem;
                background: #38bdf8;
                color: #07111f;
                font-weight: 600;
                text-decoration: none;
            }
            a:hover, a:focus-visible { background: #7dd3fc; }
            @media (prefers-color-scheme: light) {
                body { background: #f7f9fb; color: #091223; }
                p { color: #46596e; }
                .code { color: #0369a1; }
                a { background: #0369a1; color: #fff; }
                a:hover, a:focus-visible { background: #075985; }
            }
        </style>
    </head>
    <body>
        <main>
            <p class="code" aria-hidden="true">@yield('code')</p>
            <h1>@yield('title')</h1>
            <p>@yield('message')</p>
            <a href="{{ url('/') }}">Volver al inicio</a>
        </main>
    </body>
</html>
