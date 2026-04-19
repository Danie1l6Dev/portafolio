import type { Metadata } from 'next';
import { getExperiences } from '@/services/experiences';
import { ExperienceItem } from '@/components/portfolio/ExperienceItem';

export const metadata: Metadata = {
  title: 'Experiencia — Daniel Sierra',
  description: 'Trayectoria profesional y académica de Daniel Sierra.',
};

export const revalidate = 30;

export default async function ExperienciaPage() {
  let experiences: Awaited<ReturnType<typeof getExperiences>> = [];

  try {
    experiences = await getExperiences();
  } catch {
    // fallback silencioso
  }

  const current = experiences.filter((e) => e.is_current);
  const past = experiences.filter((e) => !e.is_current);

  return (
    <main className="mx-auto max-w-3xl px-4 py-14">
      {/* Header */}
      <div className="mb-12">
        <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-sky-500">
          Trayectoria
        </p>
        <h1 className="mb-3 text-3xl font-bold tracking-tight text-slate-900">
          Experiencia
        </h1>
        <p className="text-slate-500">Mi trayectoria profesional y académica.</p>
      </div>

      {experiences.length === 0 ? (
        <div className="rounded-xl border border-slate-100 bg-slate-50 p-10 text-center">
          <p className="text-sm text-slate-400">No hay experiencias registradas todavía.</p>
        </div>
      ) : (
        <div className="space-y-12">
          {/* Posición actual primero */}
          {current.length > 0 && (
            <section>
              <div className="mb-6 flex items-center gap-3">
                <h2 className="text-xs font-bold uppercase tracking-widest text-emerald-500">
                  Actualmente
                </h2>
                <div className="h-px flex-1 bg-slate-100" />
              </div>
              <div>
                {current.map((exp) => (
                  <ExperienceItem key={exp.id} experience={exp} />
                ))}
              </div>
            </section>
          )}

          {/* Historial */}
          {past.length > 0 && (
            <section>
              <div className="mb-6 flex items-center gap-3">
                <h2 className="text-xs font-bold uppercase tracking-widest text-slate-400">
                  Experiencia anterior
                </h2>
                <div className="h-px flex-1 bg-slate-100" />
              </div>
              <div>
                {past.map((exp) => (
                  <ExperienceItem key={exp.id} experience={exp} />
                ))}
              </div>
            </section>
          )}
        </div>
      )}
    </main>
  );
}
