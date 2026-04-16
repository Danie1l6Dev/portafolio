'use client';

import { useSkills } from '@/hooks/useSkills';
import { SkillBadge } from '@/components/portfolio/SkillBadge';

export default function HabilidadesPage() {
  const { skills, groups, loading, error } = useSkills();

  return (
    <main className="mx-auto max-w-3xl px-4 py-10">
      <h1 className="mb-2 text-3xl font-bold text-gray-900">Habilidades</h1>
      <p className="mb-8 text-gray-500">
        Tecnologías y herramientas que manejo en mis proyectos.
      </p>

      {loading && (
        <div className="space-y-6">
          {Array.from({ length: 3 }).map((_, i) => (
            <div key={i} className="h-24 animate-pulse rounded-xl bg-gray-100" />
          ))}
        </div>
      )}

      {error && (
        <p className="rounded-lg bg-red-50 p-4 text-sm text-red-600">{error}</p>
      )}

      {!loading && !error && (
        <>
          {groups.length > 0 ? (
            <div className="space-y-8">
              {groups.map((group) => (
                <section key={group}>
                  <h2 className="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    {group}
                  </h2>
                  <div className="flex flex-wrap gap-2">
                    {skills
                      .filter((s) => s.group === group)
                      .map((skill) => (
                        <SkillBadge key={skill.id} skill={skill} showLevel />
                      ))}
                  </div>
                </section>
              ))}

              {/* Sin grupo */}
              {skills.filter((s) => !s.group).length > 0 && (
                <section>
                  <h2 className="mb-3 text-xs font-semibold uppercase tracking-wider text-gray-400">
                    Otras
                  </h2>
                  <div className="flex flex-wrap gap-2">
                    {skills
                      .filter((s) => !s.group)
                      .map((skill) => (
                        <SkillBadge key={skill.id} skill={skill} showLevel />
                      ))}
                  </div>
                </section>
              )}
            </div>
          ) : (
            <div className="flex flex-wrap gap-2">
              {skills.map((skill) => (
                <SkillBadge key={skill.id} skill={skill} showLevel />
              ))}
            </div>
          )}

          {skills.length === 0 && (
            <p className="text-gray-400">No hay habilidades registradas todavía.</p>
          )}
        </>
      )}
    </main>
  );
}
