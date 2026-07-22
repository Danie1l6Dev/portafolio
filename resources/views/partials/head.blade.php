<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />

<title>
    {{ filled($title ?? null) ? $title.' - '.config('app.name', 'Laravel') : config('app.name', 'Laravel') }}
</title>

<link rel="icon" href="/favicon.svg?v=2" type="image/svg+xml">
<meta name="theme-color" content="#091223">

@fonts

@vite(['resources/css/app.css', 'resources/js/app.js'])
@fluxAppearance
