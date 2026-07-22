@props([
    'title' => null,
])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="admin-shell min-h-screen">
        @php
            $sidebarUnreadMessages = \App\Models\Message::query()->unread()->count();
            $adminNavigation = [
                ['label' => 'Resumen', 'route' => 'panel.dashboard', 'icon' => 'home'],
                ['label' => 'Proyectos', 'route' => 'panel.projects', 'icon' => 'folder-git-2'],
                ['label' => 'Categorías', 'route' => 'panel.categories', 'icon' => 'tag'],
                ['label' => 'Habilidades', 'route' => 'panel.skills', 'icon' => 'code-bracket'],
                ['label' => 'Experiencias', 'route' => 'panel.experiences', 'icon' => 'briefcase'],
                ['label' => 'Logros', 'route' => 'panel.achievements', 'icon' => 'trophy'],
                ['label' => 'Mensajes', 'route' => 'panel.messages', 'icon' => 'inbox'],
            ];
        @endphp

        <a href="#admin-content" class="admin-skip-link">Saltar al contenido</a>

        <div
            class="admin-layout"
            x-data="{ adminNavOpen: false }"
            x-on:keydown.escape.window="adminNavOpen = false"
            x-on:resize.window="if (window.innerWidth >= 1024) { adminNavOpen = false; document.body.style.overflow = '' }"
            x-on:livewire:navigated.window="adminNavOpen = false; document.body.style.overflow = ''"
            x-effect="document.body.style.overflow = adminNavOpen && window.innerWidth < 1024 ? 'hidden' : ''"
        >
            <div
                class="admin-sidebar-backdrop"
                x-cloak
                x-show="adminNavOpen"
                x-transition.opacity.duration.180ms
                x-on:click="adminNavOpen = false"
                aria-hidden="true"
            ></div>

            <aside
                id="admin-navigation"
                class="admin-sidebar"
                x-ref="adminNavigation"
                x-bind:data-open="adminNavOpen ? 'true' : 'false'"
                tabindex="-1"
                aria-label="Navegación del panel"
            >
                <div class="admin-sidebar__brand-row">
                    <a href="{{ route('panel.dashboard') }}" class="admin-sidebar__brand" wire:navigate x-on:click="adminNavOpen = false">
                        <x-app-logo-icon class="admin-sidebar__logo" />
                        <span class="admin-sidebar__identity">
                            <span class="admin-sidebar__title">Panel Admin</span>
                            <span class="admin-sidebar__email">{{ auth()->user()->email }}</span>
                        </span>
                    </a>

                    <button
                        type="button"
                        class="admin-sidebar__close"
                        x-on:click="adminNavOpen = false"
                        aria-label="Cerrar navegación"
                    >
                        <flux:icon.x-mark class="size-4" />
                    </button>
                </div>

                <nav class="admin-sidebar__nav" aria-label="Contenido">
                    <p class="admin-sidebar__eyebrow">Contenido</p>
                    <ul class="admin-sidebar__list">
                        @foreach ($adminNavigation as $item)
                            @php($isCurrent = request()->routeIs($item['route']) || ($item['route'] === 'panel.dashboard' && request()->routeIs('dashboard')))
                            <li>
                                <a
                                    href="{{ route($item['route']) }}"
                                    class="admin-nav-link {{ $isCurrent ? 'is-active' : '' }}"
                                    @if ($isCurrent) aria-current="page" @endif
                                    wire:navigate
                                    x-on:click="adminNavOpen = false"
                                >
                                    <span class="admin-nav-link__icon" aria-hidden="true">
                                        <flux:icon :icon="$item['icon']" variant="micro" class="size-4" />
                                    </span>
                                    <span class="admin-nav-link__label">{{ $item['label'] }}</span>

                                    @if ($item['route'] === 'panel.messages' && $sidebarUnreadMessages > 0)
                                        <span class="admin-nav-link__badge" aria-label="{{ $sidebarUnreadMessages }} mensajes sin leer">
                                            {{ $sidebarUnreadMessages > 99 ? '99+' : $sidebarUnreadMessages }}
                                        </span>
                                    @elseif ($isCurrent)
                                        <span class="admin-nav-link__current" aria-hidden="true"></span>
                                    @endif
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </nav>

                <div class="admin-sidebar__footer">
                    <a href="{{ route('home') }}" target="_blank" rel="noreferrer" class="admin-sidebar__utility-link">
                        <flux:icon.globe-alt class="size-4" />
                        <span>Ver portafolio</span>
                        <flux:icon.arrow-up-right class="admin-sidebar__external-icon" />
                    </a>
                    <a href="{{ route('profile.edit') }}" class="admin-sidebar__utility-link" wire:navigate x-on:click="adminNavOpen = false">
                        <flux:icon.cog-6-tooth class="size-4" />
                        <span>Configuración</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="admin-sidebar__utility-link admin-sidebar__utility-link--danger" data-test="logout-button">
                            <flux:icon.arrow-right-start-on-rectangle class="size-4" />
                            <span>Cerrar sesión</span>
                        </button>
                    </form>
                </div>
            </aside>

            <div class="admin-workspace">
                <header class="admin-mobilebar">
                    <button
                        type="button"
                        class="admin-mobilebar__menu"
                        x-on:click="adminNavOpen = true; $nextTick(() => $refs.adminNavigation.focus())"
                        aria-controls="admin-navigation"
                        x-bind:aria-expanded="adminNavOpen"
                    >
                        <flux:icon.bars-2 class="size-5" />
                        <span class="sr-only">Abrir navegación</span>
                    </button>

                    <a href="{{ route('panel.dashboard') }}" class="admin-mobilebar__brand" wire:navigate>
                        <x-app-logo-icon class="size-8" />
                        <span>Panel Admin</span>
                    </a>

                    <a href="{{ route('home') }}" target="_blank" rel="noreferrer" class="admin-mobilebar__portfolio" aria-label="Ver portafolio">
                        <flux:icon.arrow-up-right class="size-4" />
                    </a>
                </header>

                <main id="admin-content" class="admin-main" tabindex="-1">
                    <div class="admin-page">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
