<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureModels();
        $this->configureRateLimiting();
    }

    // ── Modelos ───────────────────────────────────────────────

    private function configureModels(): void
    {
        /*
         * Previene la carga lazy de relaciones en entorno local/testing.
         * Fuerza el uso de eager loading (->with([...])) y detecta N+1 queries
         * antes de llegar a producción.
         *
         * Desactivado en producción para no generar errores en usuarios.
         */
        Model::preventLazyLoading(! app()->isProduction());

        /*
         * Previene la asignación masiva silenciosa.
         * Lanza excepción si se intenta rellenar una columna no definida en $fillable.
         */
        Model::preventSilentlyDiscardingAttributes(! app()->isProduction());
    }

    // ── Rate Limiting ─────────────────────────────────────────

    private function configureRateLimiting(): void
    {
        /*
         * Límite general de la API pública.
         * 60 peticiones por minuto por IP.
         * Las rutas de admin quedan fuera de este límite (tienen su propia lógica de auth).
         */
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->ip());
        });

        /*
         * Límite estricto para el endpoint de login.
         * 5 intentos por minuto por IP para dificultar ataques de fuerza bruta.
         */
        RateLimiter::for('login', function (Request $request) {
            return [
                Limit::perMinute(5)->by($request->ip()),
                Limit::perMinute(3)->by($request->input('email') . '|' . $request->ip()),
            ];
        });

        /*
         * Límite para el formulario de contacto público.
         * 5 mensajes por minuto por IP.
         */
        RateLimiter::for('contact', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });
    }
}
