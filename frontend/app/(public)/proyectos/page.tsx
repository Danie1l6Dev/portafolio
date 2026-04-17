'use client';

import { useState, useMemo } from 'react';
import { useProjects } from '@/hooks/useProjects';
import { ProjectCarousel } from '@/components/portfolio/ProjectCarousel';
import { cn } from '@/lib/utils';

export default function ProyectosPage() {
  const [activeCategory, setActiveCategory] = useState<string | undefined>();

  // Carga todos los proyectos para extraer categorías únicas
  const all      = useProjects({});
  // Carga proyectos filtrados para el carrusel (sin paginación manual)
  const filtered = useProjects({ category: activeCategory });

  const categories = useMemo(() => {
    const seen = new Map<string, string>();
    all.projects.forEach((p) => {
      if (p.category && !seen.has(p.category.slug)) {
        seen.set(p.category.slug, p.category.name);
      }
    });
    return Array.from(seen.entries()).map(([slug, name]) => ({ slug, name }));
  }, [all.projects]);

  function selectCategory(slug: string | undefined) {
    setActiveCategory(slug);
  }

  const { projects, meta, loading, error } = filtered;

  return (
    <main className="mx-auto max-w-6xl px-4 py-14">

      {/* ── Header ────────────────────────────────────────── */}
      <div className="mb-10 text-center">
        <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-sky-500">
          Portafolio
        </p>
        <h1 className="text-3xl font-bold tracking-tight text-slate-900">
          Proyectos
        </h1>
        <p className="mt-2 text-slate-500">
          Una selección de trabajos y proyectos personales.
          {meta && (
            <span className="ml-1 text-slate-400">
              · {meta.total} {meta.total === 1 ? 'proyecto' : 'proyectos'}
            </span>
          )}
        </p>
      </div>

      {/* ── Filtro por categoría ───────────────────────────── */}
      {categories.length > 0 && (
        <div className="mb-10 flex flex-wrap justify-center gap-2">
          <button
            onClick={() => selectCategory(undefined)}
            className={cn(
              'rounded-full border px-4 py-1.5 text-sm font-medium transition-all duration-150',
              !activeCategory
                ? 'border-sky-600 bg-sky-600 text-white shadow-sm'
                : 'border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50',
            )}
          >
            Todos
          </button>

          {categories.map(({ slug, name }) => (
            <button
              key={slug}
              onClick={() => selectCategory(slug)}
              className={cn(
                'rounded-full border px-4 py-1.5 text-sm font-medium transition-all duration-150',
                activeCategory === slug
                  ? 'border-sky-600 bg-sky-600 text-white shadow-sm'
                  : 'border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50',
              )}
            >
              {name}
            </button>
          ))}
        </div>
      )}

      {/* ── Skeleton ──────────────────────────────────────── */}
      {loading && <CarouselSkeleton />}

      {/* ── Error ─────────────────────────────────────────── */}
      {error && !loading && (
        <div className="rounded-xl border border-red-100 bg-red-50 p-6 text-center">
          <p className="text-sm font-medium text-red-700">No se pudo cargar los proyectos</p>
          <p className="mt-1 text-xs text-red-500">{error}</p>
          <button
            onClick={() => window.location.reload()}
            className="mt-4 rounded-lg border border-red-200 bg-white px-4 py-1.5 text-xs font-medium text-red-600 transition hover:bg-red-50"
          >
            Reintentar
          </button>
        </div>
      )}

      {/* ── Carrusel ──────────────────────────────────────── */}
      {!loading && !error && (
        projects.length === 0 ? (
          <div className="rounded-2xl border border-slate-100 bg-slate-50 py-20 text-center">
            <div className="mx-auto mb-4 flex h-14 w-14 items-center justify-center rounded-full bg-white text-3xl shadow-sm ring-1 ring-slate-100">
              🗂️
            </div>
            <p className="font-medium text-slate-600">
              {activeCategory
                ? 'No hay proyectos en esta categoría'
                : 'Aún no hay proyectos publicados'}
            </p>
            <p className="mt-1 text-sm text-slate-400">
              {activeCategory
                ? 'Prueba con otra categoría o mira todos los proyectos.'
                : 'Pronto habrá contenido aquí.'}
            </p>
            {activeCategory && (
              <button
                onClick={() => selectCategory(undefined)}
                className="mt-4 rounded-lg bg-sky-600 px-5 py-1.5 text-sm font-medium text-white transition hover:bg-sky-700"
              >
                Ver todos los proyectos
              </button>
            )}
          </div>
        ) : (
          /* key fuerza el reset de activeIndex al cambiar de categoría */
          <ProjectCarousel key={activeCategory ?? 'all'} projects={projects} />
        )
      )}
    </main>
  );
}

// ── Skeleton ──────────────────────────────────────────────────

function CarouselSkeleton() {
  const items = [
    { offset: -1, scale: 0.80, opacity: 0.45 },
    { offset: 0,  scale: 1,    opacity: 1    },
    { offset: 1,  scale: 0.80, opacity: 0.45 },
  ];

  return (
    <div className="relative overflow-hidden" style={{ height: 440 }}>
      {items.map(({ offset, scale, opacity }) => (
        <div
          key={offset}
          className="absolute left-1/2 top-6 w-64 animate-pulse overflow-hidden rounded-2xl bg-slate-100 sm:w-72 md:w-80"
          style={{
            transform: `translateX(calc(-50% + ${offset * 340}px)) scale(${scale})`,
            opacity,
            transformOrigin: 'top center',
          }}
        >
          <div className="aspect-video bg-slate-200" />
          <div className="space-y-3 p-4">
            <div className="h-3 w-1/3 rounded-full bg-slate-200" />
            <div className="h-4 w-3/4 rounded bg-slate-200" />
            <div className="h-3 w-full rounded bg-slate-200" />
            <div className="h-3 w-2/3 rounded bg-slate-200" />
          </div>
        </div>
      ))}
    </div>
  );
}
