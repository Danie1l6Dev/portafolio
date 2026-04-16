import type { Metadata } from 'next';
import { getSkills } from '@/services/skills';
import { SkillBadge } from '@/components/portfolio/SkillBadge';

export const metadata: Metadata = {
  title: 'Habilidades — Daniel Sierra',
  description: 'Tecnologías y herramientas que manejo en mis proyectos.',
};

export const revalidate = 300;

export default async function HabilidadesPage() {
  let skills: Awaited<ReturnType<typeof getSkills>>['data'] = [];
  let groups: string[] = [];

  try {
    const res = await getSkills();
    skills = res.data;
    groups = res.meta.groups;
  } catch {
    // fallback silencioso
  }

  const ungrouped = skills.filter((s) => !s.group);

  return (
    <main className="mx-auto max-w-3xl px-4 py-14">
      {/* Header */}
      <div className="mb-12">
        <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-indigo-500">
          Stack técnico
        </p>
        <h1 className="mb-3 text-3xl font-bold tracking-tight text-slate-900">
          Habilidades
        </h1>
        <p className="text-slate-500">
          Tecnologías y herramientas que manejo en mis proyectos.
        </p>
      </div>

      {skills.length === 0 ? (
        <div className="rounded-xl border border-slate-100 bg-slate-50 p-10 text-center">
          <p className="text-sm text-slate-400">No hay habilidades registradas todavía.</p>
        </div>
      ) : (
        <div className="space-y-12">
          {groups.map((group) => {
            const groupSkills = skills.filter((s) => s.group === group);
            if (!groupSkills.length) return null;
            return (
              <section key={group}>
                <div className="mb-4 flex items-center gap-3">
                  <h2 className="text-xs font-bold uppercase tracking-widest text-indigo-500">
                    {group}
                  </h2>
                  <div className="h-px flex-1 bg-slate-100" />
                  <span className="text-xs text-slate-400">{groupSkills.length}</span>
                </div>
                <div className="flex flex-wrap gap-2.5">
                  {groupSkills.map((skill) => (
                    <SkillBadge key={skill.id} skill={skill} showLevel />
                  ))}
                </div>
              </section>
            );
          })}

          {ungrouped.length > 0 && (
            <section>
              <div className="mb-4 flex items-center gap-3">
                <h2 className="text-xs font-bold uppercase tracking-widest text-slate-400">
                  Otras
                </h2>
                <div className="h-px flex-1 bg-slate-100" />
                <span className="text-xs text-slate-400">{ungrouped.length}</span>
              </div>
              <div className="flex flex-wrap gap-2.5">
                {ungrouped.map((skill) => (
                  <SkillBadge key={skill.id} skill={skill} showLevel />
                ))}
              </div>
            </section>
          )}
        </div>
      )}
    </main>
  );
}
