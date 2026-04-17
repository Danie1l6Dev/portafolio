import type { ProjectStatus } from '@/types';

// ── Fechas ────────────────────────────────────────────────────

/** "2024-06-15" → "junio 2024" */
export function formatDate(iso: string | null | undefined): string {
  if (!iso) return '';
  return new Date(iso).toLocaleDateString('es-CO', {
    month: 'long',
    year: 'numeric',
    timeZone: 'UTC',
  });
}

/** "2023-01-01" + "2024-06-15" → "ene. 2023 – jun. 2024" */
export function formatDateRange(
  start: string | null,
  end: string | null,
  isCurrent = false,
): string {
  const fmt = (iso: string) =>
    new Date(iso).toLocaleDateString('es-CO', {
      month: 'short',
      year: 'numeric',
      timeZone: 'UTC',
    });
  const s = start ? fmt(start) : '';
  const e = isCurrent || !end ? 'Presente' : fmt(end);
  return `${s} – ${e}`;
}

// ── Texto ─────────────────────────────────────────────────────

/** Trunca texto a N caracteres añadiendo "…" si es necesario. */
export function truncate(text: string, max = 120): string {
  return text.length <= max ? text : text.slice(0, max).trimEnd() + '…';
}

/** Convierte texto a slug URL-friendly. */
export function slugify(text: string): string {
  return text
    .toLowerCase()
    .normalize('NFD')
    .replace(/[\u0300-\u036f]/g, '')
    .replace(/[^a-z0-9\s-]/g, '')
    .trim()
    .replace(/\s+/g, '-');
}

// ── Estado de proyectos ───────────────────────────────────────

const STATUS_LABELS: Record<ProjectStatus, string> = {
  draft: 'Borrador',
  published: 'Publicado',
  archived: 'Archivado',
};

const STATUS_COLORS: Record<ProjectStatus, string> = {
  draft: 'bg-yellow-100 text-yellow-800',
  published: 'bg-green-100 text-green-800',
  archived: 'bg-gray-100 text-gray-600',
};

export function statusLabel(status: ProjectStatus): string {
  return STATUS_LABELS[status] ?? status;
}

export function statusColor(status: ProjectStatus): string {
  return STATUS_COLORS[status] ?? '';
}

// ── Niveles de habilidades ────────────────────────────────────

/** 1-5 → "Básico" | "Intermedio" | "Avanzado" | "Experto" | "Maestría" */
export function skillLevelLabel(level: number): string {
  const labels = ['', 'Básico', 'Intermedio', 'Avanzado', 'Experto', 'Maestría'];
  return labels[level] ?? String(level);
}

// ── Clases CSS ────────────────────────────────────────────────

/** Combina clases CSS, filtrando falsy values. */
export function cn(...classes: (string | undefined | null | false)[]): string {
  return classes.filter(Boolean).join(' ');
}
