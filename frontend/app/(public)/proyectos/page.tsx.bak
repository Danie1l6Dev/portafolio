'use client';

import { useState, useMemo } from 'react';
import { useProjects } from '@/hooks/useProjects';
import { ProjectCard } from '@/components/portfolio/ProjectCard';
import { cn } from '@/lib/utils';

export default function ProyectosPage() {
  const [page, setPage] = useState(1);
  const [activeCategory, setActiveCategory] = useState<string | undefined>();

  // Carga inicial sin filtro para obtener todas las categorías disponibles
  const all = useProjects({});
  // Carga filtrada (puede ser la misma si no hay filtro)
  const filtered = useProjects({ page, category: activeCategory });

  // Extraer categorías únicas de los proyectos cargados
  const categories = useMemo(() => {
    const seen = new Map<string, string>(); // slug → name
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
    <main className="mx-auto max-w-5xl px-4 py-10">
      {/* Encabezado */}
      <div className="mb-8">
        <h1 className="mb-2 text-3xl font-bold text-gray-900">Proyectos</h1>
        <p className="text-gray-500">
          Una selección de trabajos y proyectos personales.
          {meta && (
            <span className="ml-2 text-sm text-gray-400">
              ({meta.total} en total)
            </span>
          )}
        </p>
      </div>

      {/* Filtro por categoría */}
      {categories.length > 0 && (
        <div className="mb-8 flex flex-wrap gap-2">
          <button
            onClick={() => selectCategory(undefined)}
            className={cn(
              'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
              !activeCategory
                ? 'border-indigo-600 bg-indigo-600 text-white'
                : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50',
            )}
          >
            Todos
          </button>
          {categories.map(({ slug, name }) => (
            <button
              key={slug}
              onClick={() => selectCategory(slug)}
              className={cn(
                'rounded-full border px-4 py-1.5 text-sm font-medium transition-colors',
                activeCategory === slug
                  ? 'border-indigo-600 bg-indigo-600 text-white'
                  : 'border-gray-200 text-gray-600 hover:border-gray-300 hover:bg-gray-50',
              )}
            >
              {name}
            </button>
          ))}
        </div>
      )}

      {/* Estado de carga */}
      {loading && (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, i) => (
            <div key={i} className="h-64 animate-pulse rounded-xl bg-gray-100" />
          ))}
        </div>
      )}

      {/* Error */}
      {error && !loading && (
        <p className="rounded-lg bg-red-50 p-4 text-sm text-red-600">{error}</p>
      )}

      {/* Lista */}
      {!loading && !error && (
        <>
          {projects.length === 0 ? (
            <div className="py-16 text-center">
              <p className="text-gray-400">
                {activeCategory
                  ? 'No hay proyectos en esta categoría.'
                  : 'No hay proyectos publicados todavía.'}
              </p>
              {activeCategory && (
                <button
                  onClick={() => selectCategory(undefined)}
                  className="mt-3 text-sm text-indigo-600 hover:underline"
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
            <div className="mt-10 flex items-center justify-center gap-3">
              <button
                onClick={() => setPage((p) => Math.max(1, p - 1))}
                disabled={page === 1}
                className="rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
              >
                ← Anterior
              </button>
              <span className="text-sm text-gray-400">
                Página {page} de {meta.last_page}
              </span>
              <button
                onClick={() =>
                  setPage((p) => Math.min(meta.last_page, p + 1))
                }
                disabled={page === meta.last_page}
                className="rounded-md border border-gray-200 px-4 py-2 text-sm font-medium text-gray-600 transition-colors hover:bg-gray-50 disabled:cursor-not-allowed disabled:opacity-40"
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
