<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="auth-shell">
        <div class="auth-shell__canvas">
            <a href="{{ route('home') }}" class="auth-shell__brand" wire:navigate>
                <x-app-logo-icon class="auth-shell__brand-mark" />
                <span>{{ config('app.name', 'Portafolio Daniel Sierra') }}</span>
            </a>
            <main class="auth-shell__content">
                {{ $slot }}
            </main>
        </div>

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
