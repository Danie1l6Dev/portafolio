'use client';

import { useState } from 'react';
import { useProjects } from '@/hooks/useProjects';
import { ProjectCard } from '@/components/portfolio/ProjectCard';

export default function ProyectosPage() {
  const [page, setPage] = useState(1);
  const { projects, meta, loading, error } = useProjects({ page });

  return (
    <main className="mx-auto max-w-5xl px-4 py-10">
      <h1 className="mb-2 text-3xl font-bold text-gray-900">Proyectos</h1>
      <p className="mb-8 text-gray-500">
        Una selección de trabajos y proyectos personales.
      </p>

      {loading && (
        <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
          {Array.from({ length: 6 }).map((_, i) => (
            <div key={i} className="h-64 animate-pulse rounded-xl bg-gray-100" />
          ))}
        </div>
      )}

      {error && (
        <p className="rounded-lg bg-red-50 p-4 text-sm text-red-600">{error}</p>
      )}

      {!loading && !error && (
        <>
          {projects.length === 0 ? (
            <p className="text-gray-400">No hay proyectos publicados todavía.</p>
          ) : (
            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {projects.map((project) => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>
          )}

          {/* Paginación simple */}
          {meta && meta.last_page > 1 && (
            <div className="mt-10 flex justify-center gap-2">
              <button
                onClick={() => setPage((p) => Math.max(1, p - 1))}
                disabled={page === 1}
                className="rounded-md border px-4 py-2 text-sm disabled:opacity-40"
              >
                ← Anterior
              </button>
              <span className="flex items-center px-3 text-sm text-gray-500">
                {page} / {meta.last_page}
              </span>
              <button
                onClick={() => setPage((p) => Math.min(meta.last_page, p + 1))}
                disabled={page === meta.last_page}
                className="rounded-md border px-4 py-2 text-sm disabled:opacity-40"
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
