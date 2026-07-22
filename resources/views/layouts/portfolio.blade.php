<!DOCTYPE html>
<html lang="es" class="dark scroll-smooth" data-theme="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        @php
            $pageTitle = filled($title ?? null)
                ? $title.' — '.config('portfolio.name')
                : config('portfolio.name').' — '.config('portfolio.role');
            $pageDescription = $description ?? config('portfolio.description');
            $pageCanonical = $canonical ?? url()->current();
            $pageImage = $image ?? null;
            $pageType = $type ?? 'website';
        @endphp

        <title>{{ $pageTitle }}</title>
        <meta name="description" content="{{ $pageDescription }}">
        <link rel="canonical" href="{{ $pageCanonical }}">

        <meta property="og:locale" content="es_CO">
        <meta property="og:type" content="{{ $pageType }}">
        <meta property="og:site_name" content="{{ config('portfolio.name') }}">
        <meta property="og:title" content="{{ $pageTitle }}">
        <meta property="og:description" content="{{ $pageDescription }}">
        <meta property="og:url" content="{{ $pageCanonical }}">
        @if ($pageImage)
            <meta property="og:image" content="{{ $pageImage }}">
        @endif

        <meta name="twitter:card" content="{{ $pageImage ? 'summary_large_image' : 'summary' }}">
        <meta name="twitter:title" content="{{ $pageTitle }}">
        <meta name="twitter:description" content="{{ $pageDescription }}">
        @if ($pageImage)
            <meta name="twitter:image" content="{{ $pageImage }}">
        @endif

        <link rel="icon" href="/favicon.svg?v=2" type="image/svg+xml">
        <meta id="portfolio-theme-color" name="theme-color" content="#07111f">
        <script>
            (() => {
                const storageKey = 'portfolio-theme';
                let theme = 'dark';

                try {
                    theme = window.localStorage.getItem(storageKey) === 'light' ? 'light' : 'dark';
                } catch (_) {
                    theme = 'dark';
                }

                const root = document.documentElement;
                root.classList.toggle('dark', theme === 'dark');
                root.dataset.theme = theme;
                root.style.colorScheme = theme;

                const themeColor = document.querySelector('#portfolio-theme-color');
                themeColor?.setAttribute('content', theme === 'dark' ? '#07111f' : '#f7f9fb');
            })();
        </script>

        @if (isset($schema))
            <script type="application/ld+json">{!! json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
        @endif

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="portfolio-shell min-h-screen overflow-x-hidden bg-paper-50 text-ink-950 antialiased dark:bg-[#07111f] dark:text-slate-100">
        <a href="#contenido" class="skip-link">Saltar al contenido</a>

        <x-portfolio.navigation
            :show-experience="isset($experiences) && $experiences->isNotEmpty()"
            :show-achievements="isset($achievements) && $achievements->isNotEmpty()"
        />

        <main id="contenido">
            @isset($slot)
                {{ $slot }}
            @else
                @yield('content')
            @endisset
        </main>

        <x-portfolio.footer />

        @livewireScripts
    </body>
</html>
