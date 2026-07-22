<x-layouts::auth :title="__('Log in')">
    <div class="auth-login-card">
        <header class="auth-login-card__header">
            <x-app-logo-icon class="auth-login-card__mark" />
            <h1>Panel Admin</h1>
            <p>Inicia sesión para continuar</p>
        </header>

        <x-auth-session-status class="auth-login-card__status" :status="session('status')" />

        <x-passkey-verify />

        <form method="POST" action="{{ route('login.store') }}" class="auth-login-form">
            @csrf

            <flux:input
                name="email"
                label="Correo electrónico"
                :value="old('email')"
                type="email"
                required
                autofocus
                autocomplete="email"
                placeholder="admin@ejemplo.com"
            />

            <div class="auth-login-form__password">
                <flux:input
                    name="password"
                    label="Contraseña"
                    type="password"
                    required
                    autocomplete="current-password"
                    placeholder="••••••••"
                    viewable
                />

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate>¿Olvidaste tu contraseña?</a>
                @endif
            </div>

            <flux:checkbox name="remember" label="Recordarme" :checked="old('remember')" />

            <div class="auth-login-form__actions">
                <flux:button variant="primary" type="submit" class="w-full" data-test="login-button">
                    Iniciar sesión
                </flux:button>
            </div>
        </form>

        <p class="auth-login-card__note">Acceso restringido para administrar tu portafolio.</p>
    </div>
</x-layouts::auth>
