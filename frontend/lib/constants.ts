/**
 * Constantes globales del sitio.
 *
 * Centraliza valores que se repiten en múltiples páginas y componentes:
 * metadatos del sitio, links sociales, configuración de paginación, etc.
 *
 * Uso:
 *   import { SITE, SOCIAL_LINKS } from '@/lib/constants';
 *   <title>{SITE.name}</title>
 */

// ── Metadatos del sitio ───────────────────────────────────────

export const SITE = {
  name:        process.env.NEXT_PUBLIC_APP_NAME ?? 'Daniel Sierra',
  description: 'Desarrollador de software. Proyectos, habilidades y experiencia profesional.',
  url:         process.env.NEXT_PUBLIC_APP_URL  ?? 'http://localhost:3000',
  author:      'Daniel Sierra',
  locale:      'es_CO',
  email:       'danielsierra103@gmail.com',
} as const;

// ── Redes sociales ────────────────────────────────────────────

export const SOCIAL_LINKS = [
  { label: 'GitHub',    icon: 'github',    href: 'https://github.com/Danie1l6Dev',        rel: 'noopener noreferrer' },
  { label: 'LinkedIn',  icon: 'linkedin',  href: 'https://www.linkedin.com/in/daniel-sierra-44262a3b6/?trk=public-profile-join-page', rel: 'noopener noreferrer' },
  { label: 'Instagram', icon: 'instagram', href: 'https://instagram.com/danie1l6',         rel: 'noopener noreferrer' },
] as const;

// ── Navegación pública ────────────────────────────────────────

export const NAV_LINKS = [
  { href: '/',           label: 'Inicio' },
  { href: '/proyectos',  label: 'Proyectos' },
  { href: '/habilidades',label: 'Habilidades' },
  { href: '/experiencia',label: 'Experiencia' },
  { href: '/contacto',   label: 'Contacto' },
] as const;

// ── Paginación ────────────────────────────────────────────────

export const PAGINATION = {
  /** Proyectos por página en el listado público. */
  projectsPerPage: 9,
  /** Elementos por página en el panel admin. */
  adminPerPage: 15,
} as const;

// ── Límites de contenido ──────────────────────────────────────

export const LIMITS = {
  projectTitle:    200,
  projectSummary:  500,
  contactName:     100,
  contactSubject:  150,
  contactBody:    3000,
  skillName:       100,
  categoryName:    100,
  imageMaxMB:        2,
  imageMaxBytes:  2 * 1024 * 1024,
} as const;

// ── Revalidación ISR (segundos) ───────────────────────────────

export const REVALIDATE = {
  /** Páginas de contenido que cambia poco (habilidades, experiencia). */
  slow:    300, // 5 min
  /** Páginas con contenido más dinámico (proyectos). */
  medium:   60, // 1 min
  /** Home con datos en tiempo real. */
  fast:     30, // 30 s
} as const;

// ── API ───────────────────────────────────────────────────────

export const API = {
  /** Tiempo máximo de espera de la API en ms. */
  timeoutMs: 5_000,
} as const;


// ── Auth ──────────────────────────────────────────────────────

export const TOKEN_COOKIE = 'token';

/*
 * ── Escalabilidad ────────────────────────────────────────────
 *
 * Para añadir blog:
 *   Añade una entrada en NAV_LINKS: { href: '/blog', label: 'Blog' }
 *   Crea las páginas en app/(public)/blog/
 *
 * Para múltiples idiomas (i18n):
 *   Sustituye SITE.locale y ajusta los locales de Next.js en next.config.ts
 *   Usa next-intl o next-i18next
 *
 * Para temas (dark mode):
 *   Añade una variable --theme: 'light' | 'dark' en globals.css
 *   Usa next-themes para el toggle
 */
