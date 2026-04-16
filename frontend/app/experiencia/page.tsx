'use client';

import { useExperiences } from '@/hooks/useExperiences';
import { ExperienceItem } from '@/components/portfolio/ExperienceItem';

export default function ExperienciaPage() {
  const { experiences, loading, error } = useExperiences();

  return (
    <main className="mx-auto max-w-3xl px-4 py-10">
      <h1 className="mb-2 text-3xl font-bold text-gray-900">Experiencia</h1>
      <p className="mb-8 text-gray-500">
        Mi trayectoria profesional y académica.
      </p>

      {loading && (
        <div className="space-y-6">
          {Array.from({ length: 3 }).map((_, i) => (
            <div key={i} className="h-20 animate-pulse rounded-xl bg-gray-100" />
          ))}
        </div>
      )}

      {error && (
        <p className="rounded-lg bg-red-50 p-4 text-sm text-red-600">{error}</p>
      )}

      {!loading && !error && (
        <div className="space-y-8">
          {experiences.length === 0 ? (
            <p className="text-gray-400">No hay experiencias registradas todavía.</p>
          ) : (
            experiences.map((exp) => (
              <ExperienceItem key={exp.id} experience={exp} />
            ))
          )}
        </div>
      )}
    </main>
  );
}
