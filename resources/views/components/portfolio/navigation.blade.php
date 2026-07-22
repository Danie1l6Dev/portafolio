@props(['showExperience' => false])

@php
    $items = [
        ['id' => 'inicio', 'label' => 'Inicio'],
        ['id' => 'sobre-mi', 'label' => 'Sobre mí'],
        ['id' => 'proyectos', 'label' => 'Proyectos'],
        ['id' => 'habilidades', 'label' => 'Habilidades'],
    ];

    if ($showExperience) {
        $items[] = ['id' => 'experiencia', 'label' => 'Experiencia'];
    }

    $items[] = ['id' => 'contacto', 'label' => 'Contacto'];

    $initialSection = match (true) {
        request()->routeIs('portfolio.projects.*') => 'proyectos',
        request()->routeIs('home') => 'inicio',
        default => null,
    };
@endphp

<header
    x-data="portfolioNavigation({{ Js::from(collect($items)->pluck('id')) }}, {{ Js::from($initialSection) }})"
    @keydown.escape.window="closeMenu()"
    class="portfolio-nav"
>
    <nav class="portfolio-container flex h-[4.5rem] items-center justify-between gap-4" aria-label="Navegación principal">
        <a href="{{ route('home') }}#inicio" aria-label="Ir al inicio de {{ config('portfolio.name') }}" class="group inline-flex min-h-11 items-center gap-3 rounded-xl focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-500 focus-visible:ring-offset-4 focus-visible:ring-offset-paper-50">
            <img
                src="{{ asset('favicon.svg') }}?v=2"
                alt=""
                width="36"
                height="36"
                class="size-9 rounded-xl shadow-sm transition-transform duration-200 group-hover:-rotate-3"
                data-portfolio-mark
            >
            <span class="hidden text-sm font-semibold tracking-tight text-ink-950 sm:block">{{ config('portfolio.name') }}</span>
        </a>

        <div class="hidden items-center rounded-2xl border border-ink-950/8 bg-white/70 p-1 shadow-sm backdrop-blur-xl lg:flex">
            @foreach ($items as $item)
                <a
                    href="{{ route('home') }}#{{ $item['id'] }}"
                    @click="closeMenu()"
                    :aria-current="active === '{{ $item['id'] }}' ? 'location' : null"
                    :class="active === '{{ $item['id'] }}' ? 'bg-ink-950 text-white shadow-sm' : 'text-ink-600 hover:bg-paper-100 hover:text-ink-950'"
                    class="min-h-10 rounded-xl px-4 py-2.5 text-sm font-medium transition-[color,background-color,box-shadow] duration-200 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-500"
                >{{ $item['label'] }}</a>
            @endforeach
        </div>

        <a href="{{ route('home') }}#contacto" class="portfolio-button portfolio-button--small hidden sm:inline-flex lg:flex">
            Hablemos
            <span aria-hidden="true">↗</span>
        </a>

        <button
            type="button"
            class="grid size-11 place-items-center rounded-xl border border-ink-950/10 bg-white text-ink-950 shadow-sm sm:ms-auto lg:hidden"
            @click="toggleMenu()"
            :aria-expanded="open.toString()"
            aria-controls="mobile-navigation"
            :aria-label="open ? 'Cerrar menú' : 'Abrir menú'"
        >
            <svg x-show="!open" aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" d="M4 7h16M4 12h16M4 17h16" />
            </svg>
            <svg x-cloak x-show="open" aria-hidden="true" class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                <path stroke-linecap="round" d="m6 6 12 12M18 6 6 18" />
            </svg>
        </button>
    </nav>

    <div
        id="mobile-navigation"
        x-cloak
        x-show="open"
        x-transition:enter="transition duration-200 ease-out"
        x-transition:enter-start="-translate-y-2 opacity-0"
        x-transition:enter-end="translate-y-0 opacity-100"
        x-transition:leave="transition duration-150 ease-in"
        x-transition:leave-start="translate-y-0 opacity-100"
        x-transition:leave-end="-translate-y-2 opacity-0"
        class="absolute inset-x-0 top-full border-t border-ink-950/8 bg-paper-50/98 px-4 pb-5 pt-3 shadow-lg backdrop-blur-xl lg:hidden"
    >
        <div class="portfolio-container grid gap-1">
            @foreach ($items as $item)
                <a
                    href="{{ route('home') }}#{{ $item['id'] }}"
                    @click="closeMenu()"
                    :aria-current="active === '{{ $item['id'] }}' ? 'location' : null"
                    :class="active === '{{ $item['id'] }}' ? 'bg-ink-950 text-white' : 'text-ink-700 hover:bg-white'"
                    class="flex min-h-12 items-center justify-between rounded-xl px-4 text-base font-medium focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-500"
                >
                    {{ $item['label'] }}
                    <span aria-hidden="true" class="font-mono text-xs opacity-50">0{{ $loop->iteration }}</span>
                </a>
            @endforeach
        </div>
    </div>
</header>
