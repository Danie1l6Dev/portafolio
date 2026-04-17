# Portafolio Daniel Sierra

Monorepo con backend Laravel API y frontend Next.js.

## Estructura

```
portafolio/
├── backend/   # Laravel 13 – API REST
└── frontend/  # Next.js 16 – Cliente
```

## Requisitos

| Herramienta | Versión mínima |
|-------------|----------------|
| PHP         | 8.2            |
| Composer    | 2.x            |
| Node.js     | 20.x           |
| MySQL       | 8.x            |

## Configuración inicial

### Backend

```bash
cd backend
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
php artisan serve   # http://localhost:8000
```

### Frontend

```bash
cd frontend
cp .env.example .env.local
npm install
npm run dev         # http://localhost:3000
```

## Ramas

| Rama      | Uso                        |
|-----------|----------------------------|
| `main`    | Producción (estable)       |
| `develop` | Desarrollo activo          |

> Todo el trabajo se hace sobre `develop`. Nunca se hace push directo a `main`.
