import type { NextConfig } from 'next';

// Hostname del backend donde viven las imágenes almacenadas
const API_HOST = process.env.NEXT_PUBLIC_API_URL
  ? new URL(process.env.NEXT_PUBLIC_API_URL).hostname
  : 'localhost';

// ── Cabeceras de seguridad HTTP ───────────────────────────────

const SECURITY_HEADERS = [
  // Evita que el sitio sea embebido en un iframe externo (clickjacking)
  { key: 'X-Frame-Options', value: 'SAMEORIGIN' },
  // Impide que el navegador infiera el MIME type (sniffing)
  { key: 'X-Content-Type-Options', value: 'nosniff' },
  // Controla la información del referrer al navegar a otros sitios
  { key: 'Referrer-Policy', value: 'strict-origin-when-cross-origin' },
  // Desactiva características sensibles del navegador que no se necesitan
  {
    key: 'Permissions-Policy',
    value: 'camera=(), microphone=(), geolocation=()',
  },
  // Fuerza HTTPS en producción (1 año, incluye subdominios)
  // Solo activo en producción para no romper el desarrollo local
  ...(process.env.NODE_ENV === 'production'
    ? [{ key: 'Strict-Transport-Security', value: 'max-age=31536000; includeSubDomains' }]
    : []),
];

const nextConfig: NextConfig = {
  // ── Imágenes remotas ────────────────────────────────────────
  images: {
    remotePatterns: [
      // Dev: http://localhost:8000/storage/**
      {
        protocol: 'http',
        hostname: API_HOST,
        port: '8000',
        pathname: '/storage/**',
      },
      // Producción: https://api.tudominio.com/storage/**
      {
        protocol: 'https',
        hostname: API_HOST,
        pathname: '/storage/**',
      },
      // Simple Icons CDN — iconos oficiales de tecnologías
      {
        protocol: 'https',
        hostname: 'cdn.simpleicons.org',
      },
    ],
  },

  // ── Cabeceras HTTP de seguridad ─────────────────────────────
  async headers() {
    return [
      {
        // Aplica a todas las rutas del sitio
        source: '/(.*)',
        headers: SECURITY_HEADERS,
      },
    ];
  },

  /*
   * ── Escalabilidad ────────────────────────────────────────────
   *
   * Para soporte i18n (múltiples idiomas):
   *   i18n: { locales: ['es', 'en'], defaultLocale: 'es' }
   *
   * Para PWA / app instalable (app móvil web):
   *   Añadir next-pwa y configurar manifest.json
   *   npm install next-pwa
   *
   * Para análisis del bundle:
   *   npm install @next/bundle-analyzer
   *   const withBundleAnalyzer = require('@next/bundle-analyzer')({ enabled: process.env.ANALYZE === 'true' })
   *   export default withBundleAnalyzer(nextConfig)
   */
};

export default nextConfig;
