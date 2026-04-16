import Link from 'next/link';
import Image from 'next/image';
import { getProjects } from '@/services/projects';
import { getSkills } from '@/services/skills';
import { getExperiences } from '@/services/experiences';
import { ProjectCard } from '@/components/portfolio/ProjectCard';
import { SkillBadge } from '@/components/portfolio/SkillBadge';
import { Badge } from '@/components/ui/Badge';
import { formatDateRange } from '@/lib/utils';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: 'Daniel Sierra — Portafolio',
  description:
    'Desarrollador de software. Proyectos, habilidades y experiencia profesional.',
};

export const revalidate = 300;

export default async function HomePage() {
  const [projectsRes, skillsRes, experiencesRes] = await Promise.allSettled([
    getProjects({ featured: true }),
    getSkills(),
    getExperiences(),
  ]);

  const featuredProjects =
    projectsRes.status === 'fulfilled' ? projectsRes.value.data : [];
  const featuredSkills =
    skillsRes.status === 'fulfilled'
      ? skillsRes.value.data.filter((s) => s.is_featured).slice(0, 12)
      : [];
  const currentJob =
    experiencesRes.status === 'fulfilled'
      ? experiencesRes.value.find((e) => e.is_current) ?? null
      : null;

  return (
    <main>
      {/* ── Hero ─────────────────────────────────────────── */}
      <section className="relative overflow-hidden">
        {/* Dot grid background */}
        <div className="bg-dot-grid absolute inset-0 opacity-60" />
        {/* Radial fade */}
        <div className="absolute inset-0 bg-gradient-to-b from-white/0 via-white/60 to-white" />

        <div className="relative mx-auto max-w-5xl px-4 py-24 sm:py-32">
          <div className="max-w-2xl animate-slide-up">
            {currentJob && (
              <p className="mb-5 inline-flex items-center gap-2 rounded-full border border-emerald-200 bg-emerald-50 px-3.5 py-1 text-sm text-emerald-700">
                <span className="h-2 w-2 animate-pulse rounded-full bg-emerald-500" />
                {currentJob.position} en {currentJob.company}
              </p>
            )}

            <h1 className="mb-5 text-4xl font-bold leading-tight tracking-tight text-slate-900 sm:text-5xl lg:text-6xl">
              Hola, soy{' '}
              <span className="bg-gradient-to-r from-indigo-600 to-violet-600 bg-clip-text text-transparent">
                Daniel Sierra
              </span>
            </h1>

            <p className="mb-9 text-lg leading-relaxed text-slate-500 sm:text-xl">
              Desarrollador de software apasionado por construir productos digitales
              útiles, bien diseñados y fáciles de usar. Me especializo en
              aplicaciones web modernas con tecnologías actuales.
            </p>

            <div className="flex flex-wrap gap-3">
              <Link
                href="/proyectos"
                className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:bg-indigo-700 hover:shadow-md active:scale-95"
              >
                Ver proyectos
                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
              <Link
                href="/contacto"
                className="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-sm transition-all duration-150 hover:border-slate-300 hover:bg-slate-50 active:scale-95"
              >
                Contactar
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* ── Proyectos destacados ──────────────────────────── */}
      {featuredProjects.length > 0 && (
        <section className="border-t border-slate-100 py-20">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-10 flex items-end justify-between">
              <div>
                <p className="mb-1.5 text-xs font-semibold uppercase tracking-widest text-indigo-500">
                  Trabajo reciente
                </p>
                <h2 className="text-2xl font-bold tracking-tight text-slate-900">
                  Proyectos destacados
                </h2>
              </div>
              <Link
                href="/proyectos"
                className="hidden items-center gap-1 text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-800 sm:flex"
              >
                Ver todos
                <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
            </div>

            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {featuredProjects.map((project) => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>

            <div className="mt-8 sm:hidden">
              <Link href="/proyectos" className="text-sm font-medium text-indigo-600 hover:underline">
                Ver todos los proyectos →
              </Link>
            </div>
          </div>
        </section>
      )}

      {/* ── Habilidades destacadas ────────────────────────── */}
      {featuredSkills.length > 0 && (
        <section className="border-t border-slate-100 bg-slate-50/60 py-20">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-10 flex items-end justify-between">
              <div>
                <p className="mb-1.5 text-xs font-semibold uppercase tracking-widest text-indigo-500">
                  Stack técnico
                </p>
                <h2 className="text-2xl font-bold tracking-tight text-slate-900">
                  Habilidades principales
                </h2>
              </div>
              <Link
                href="/habilidades"
                className="hidden items-center gap-1 text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-800 sm:flex"
              >
                Ver todas
                <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
            </div>

            <div className="flex flex-wrap gap-2.5">
              {featuredSkills.map((skill) => (
                <SkillBadge key={skill.id} skill={skill} showLevel />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* ── Experiencia actual ────────────────────────────── */}
      {currentJob && (
        <section className="border-t border-slate-100 py-20">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-10 flex items-end justify-between">
              <div>
                <p className="mb-1.5 text-xs font-semibold uppercase tracking-widest text-emerald-500">
                  Actualmente
                </p>
                <h2 className="text-2xl font-bold tracking-tight text-slate-900">
                  Experiencia
                </h2>
              </div>
              <Link
                href="/experiencia"
                className="hidden items-center gap-1 text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-800 sm:flex"
              >
                Ver trayectoria
                <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
            </div>

            <div className="rounded-2xl border border-slate-200 bg-white p-6 shadow-[0_1px_3px_0_rgb(0_0_0_/_0.07)]">
              <div className="flex items-start gap-5">
                {/* Logo */}
                <div className="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-slate-200 bg-slate-50">
                  {currentJob.company_logo ? (
                    <Image
                      src={currentJob.company_logo}
                      alt={currentJob.company}
                      fill
                      className="object-contain p-1.5"
                      sizes="48px"
                    />
                  ) : (
                    <div className="flex h-full items-center justify-center text-xl font-bold text-slate-300">
                      {currentJob.company.charAt(0).toUpperCase()}
                    </div>
                  )}
                </div>

                {/* Info */}
                <div className="flex-1">
                  <div className="mb-1 flex flex-wrap items-center gap-2">
                    <h3 className="font-semibold text-slate-900">
                      {currentJob.position}
                    </h3>
                    <Badge variant="success">Actual</Badge>
                  </div>
                  <p className="text-sm text-indigo-600">
                    {currentJob.company_url ? (
                      <a
                        href={currentJob.company_url}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="hover:underline"
                      >
                        {currentJob.company}
                      </a>
                    ) : (
                      currentJob.company
                    )}
                    {currentJob.location && (
                      <span className="text-slate-400"> · {currentJob.location}</span>
                    )}
                  </p>
                  <p className="mt-0.5 text-xs text-slate-400">
                    {formatDateRange(
                      currentJob.started_at,
                      currentJob.finished_at,
                      currentJob.is_current,
                    )}
                  </p>
                  {currentJob.description && (
                    <p className="mt-3 max-w-prose text-sm leading-relaxed text-slate-600">
                      {currentJob.description}
                    </p>
                  )}
                </div>
              </div>
            </div>
          </div>
        </section>
      )}

      {/* ── CTA Contacto ──────────────────────────────────── */}
      <section className="border-t border-slate-100">
        <div className="relative overflow-hidden bg-gradient-to-br from-indigo-600 via-indigo-700 to-violet-700 py-20">
          {/* Decorative blobs */}
          <div className="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-white/5 blur-3xl" />
          <div className="absolute -bottom-20 -left-20 h-64 w-64 rounded-full bg-white/5 blur-3xl" />

          <div className="relative mx-auto max-w-2xl px-4 text-center">
            <h2 className="mb-4 text-2xl font-bold tracking-tight text-white sm:text-3xl">
              ¿Tienes un proyecto en mente?
            </h2>
            <p className="mb-8 text-indigo-200">
              Estoy disponible para proyectos freelance, colaboraciones y nuevas
              oportunidades. Hablemos.
            </p>
            <Link
              href="/contacto"
              className="inline-flex items-center gap-2 rounded-lg bg-white px-8 py-3 text-sm font-semibold text-indigo-700 shadow-lg transition-all duration-150 hover:bg-indigo-50 hover:shadow-xl active:scale-95"
            >
              Enviar mensaje
              <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </Link>
          </div>
        </div>
      </section>
    </main>
  );
}
