'use client';

import { useState, useMemo } from 'react';
import { useProjects } from '@/hooks/useProjects';
import { ProjectCard } from '@/components/portfolio/ProjectCard';
import { cn } from '@/lib/utils';

export default function ProyectosPage() {
  const [page, setPage] = useState(1);
  const [activeCategory, setActiveCategory] = useState<string | undefined>();

  const all = useProjects({});
  const filtered = useProjects({ page, category: activeCategory });

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
    setPage(1);
  }

  const { projects, meta, loading, error } = filtered;

  return (
    <main className="mx-auto max-w-5xl px-4 py-14">
      {/* Header */}
      <div className="mb-10">
        <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-indigo-500">
          Portafolio
        </p>
        <div className="flex items-end justify-between gap-4">
          <h1 className="text-3xl font-bold tracking-tight text-slate-900">
            Proyectos
          </h1>
          {meta && (
            <span className="text-sm text-slate-400">
              {meta.total} {meta.total === 1 ? 'proyecto' : 'proyectos'}
            </span>
          )}
        </div>
        <p className="mt-2 text-slate-500">
          Una selección de trabajos y proyectos personales.
        </p>
      </div>

      {/* Filtro por categoría */}
      {categories.length > 0 && (
        <div className="mb-8 flex flex-wrap gap-2">
          <button
            onClick={() => selectCategory(undefined)}
            className={cn(
              'rounded-full border px-4 py-1.5 text-sm font-medium transition-all duration-150',
              !activeCategory
                ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm'
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
                  ? 'border-indigo-600 bg-indigo-600 text-white shadow-sm'
                  : 'border-slate-200 text-slate-600 hover:border-slate-300 hover:bg-slate-50',
              )}
            >
              {name}
            </button>
          ))}
        </div>
      )}

      {/* Skeleton loading */}
      {loading && (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, i) => (
            <div
              key={i}
              className="h-72 animate-pulse rounded-xl bg-slate-100"
              style={{ animationDelay: `${i * 60}ms` }}
            />
          ))}
        </div>
      )}

      {/* Error */}
      {error && !loading && (
        <div className="rounded-xl border border-red-100 bg-red-50 p-5 text-sm text-red-600">
          {error}
        </div>
      )}

      {/* Lista */}
      {!loading && !error && (
        <>
          {projects.length === 0 ? (
            <div className="py-20 text-center">
              <div className="mx-auto mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl text-slate-400">
                ◻
              </div>
              <p className="text-slate-400">
                {activeCategory
                  ? 'No hay proyectos en esta categoría.'
                  : 'No hay proyectos publicados todavía.'}
              </p>
              {activeCategory && (
                <button
                  onClick={() => selectCategory(undefined)}
                  className="mt-3 text-sm font-medium text-indigo-600 hover:underline"
                >
                  Ver todos los proyectos
                </button>
              )}
            </div>
          ) : (
            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {projects.map((project) => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>
          )}

          {/* Paginación */}
          {meta && meta.last_page > 1 && (
            <div className="mt-12 flex items-center justify-center gap-3">
              <button
                onClick={() => setPage((p) => Math.max(1, p - 1))}
                disabled={page === 1}
                className="inline-flex h-9 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-sm font-medium text-slate-600 shadow-sm transition-all hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
              >
                ← Anterior
              </button>
              <span className="text-sm text-slate-400">
                {page} / {meta.last_page}
              </span>
              <button
                onClick={() => setPage((p) => Math.min(meta.last_page, p + 1))}
                disabled={page === meta.last_page}
                className="inline-flex h-9 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-4 text-sm font-medium text-slate-600 shadow-sm transition-all hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
              >
                Siguiente →
              </button>
            </div>
          )}
        </>
      )}
    </main>
  );
}
