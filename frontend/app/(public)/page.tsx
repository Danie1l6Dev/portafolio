import Link from 'next/link';
import Image from 'next/image';
import { getProjects } from '@/services/projects';
import { getSkills } from '@/services/skills';
import { getExperiences } from '@/services/experiences';
import { ProjectCard } from '@/components/portfolio/ProjectCard';
import { SkillBadge } from '@/components/portfolio/SkillBadge';
import { Badge } from '@/components/ui/Badge';
import { formatDateRange, cn } from '@/lib/utils';
import { SITE, REVALIDATE } from '@/lib/constants';
import type { Metadata } from 'next';

export const metadata: Metadata = {
  title: `${SITE.author} — Portafolio`,
  description: SITE.description,
};

export const revalidate = REVALIDATE.slow;

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
        {/* Malla radial superior */}
        <div className="bg-hero-mesh absolute inset-0" />
        {/* Dot grid */}
        <div className="bg-dot-grid absolute inset-0 opacity-40" />
        {/* Fade hacia el fondo de la sección */}
        <div className="absolute inset-0 bg-gradient-to-b from-transparent via-transparent to-[#F0F7FF]" />

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
              <span className="bg-gradient-to-r from-sky-500 to-blue-600 bg-clip-text text-transparent">
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
                className="inline-flex items-center gap-2 rounded-lg bg-sky-600 px-6 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-150 hover:bg-sky-700 hover:shadow-md active:scale-95"
              >
                Ver proyectos
                <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
              <Link
                href="/contacto"
                className="inline-flex items-center gap-2 rounded-lg border border-sky-200 bg-white px-6 py-3 text-sm font-semibold text-sky-700 shadow-sm transition-all duration-150 hover:border-sky-300 hover:bg-sky-50 active:scale-95"
              >
                Contactar
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* ── Proyectos destacados ──────────────────────────── */}
      {featuredProjects.length > 0 && (
        <section className="border-t border-blue-100 py-20">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-10 flex items-end justify-between">
              <div>
                <p className="mb-1.5 text-xs font-semibold uppercase tracking-widest text-sky-500">
                  Trabajo reciente
                </p>
                <h2 className="text-2xl font-bold tracking-tight text-slate-900">
                  Proyectos destacados
                </h2>
              </div>
              <Link
                href="/proyectos"
                className="hidden items-center gap-1 text-sm font-medium text-sky-600 transition-colors hover:text-sky-800 sm:flex"
              >
                Ver todos
                <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
            </div>

            <div className="flex flex-wrap justify-center gap-6">
              {featuredProjects.map((project) => (
                <div
                  key={project.id}
                  className={cn(
                    'flex',
                    featuredProjects.length === 1
                      ? 'w-full max-w-2xl'          // 1 proyecto: ancho generoso centrado
                      : featuredProjects.length === 2
                      ? 'w-full sm:w-[calc(50%-12px)]'  // 2 proyectos: mitad
                      : 'w-full sm:w-[calc(50%-12px)] lg:w-[calc(33.333%-16px)]' // 3+
                  )}
                >
                  <ProjectCard project={project} />
                </div>
              ))}
            </div>

            <div className="mt-8 sm:hidden">
              <Link href="/proyectos" className="text-sm font-medium text-sky-600 hover:underline">
                Ver todos los proyectos →
              </Link>
            </div>
          </div>
        </section>
      )}

      {/* ── Habilidades destacadas ────────────────────────── */}
      {featuredSkills.length > 0 && (
        <section className="border-t border-blue-100 bg-[#e8f1fa] py-20">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-10 flex items-end justify-between">
              <div>
                <p className="mb-1.5 text-xs font-semibold uppercase tracking-widest text-sky-500">
                  Stack técnico
                </p>
                <h2 className="text-2xl font-bold tracking-tight text-slate-900">
                  Habilidades principales
                </h2>
              </div>
              <Link
                href="/habilidades"
                className="hidden items-center gap-1 text-sm font-medium text-sky-600 transition-colors hover:text-sky-800 sm:flex"
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
        <section className="border-t border-blue-100 py-20">
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
                className="hidden items-center gap-1 text-sm font-medium text-sky-600 transition-colors hover:text-sky-800 sm:flex"
              >
                Ver trayectoria
                <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                  <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
            </div>

            <div className="rounded-2xl border border-blue-100 bg-white p-6 shadow-[0_1px_3px_0_rgb(2_132_199_/_0.08)]">
              <div className="flex items-start gap-5">
                <div className="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-xl border border-blue-100 bg-sky-50">
                  {currentJob.company_logo ? (
                    <Image
                      src={currentJob.company_logo}
                      alt={currentJob.company}
                      fill
                      className="object-contain p-1.5"
                      sizes="48px"
                    />
                  ) : (
                    <div className="flex h-full items-center justify-center text-xl font-bold text-sky-300">
                      {currentJob.company.charAt(0).toUpperCase()}
                    </div>
                  )}
                </div>

                <div className="flex-1">
                  <div className="mb-1 flex flex-wrap items-center gap-2">
                    <h3 className="font-semibold text-slate-900">
                      {currentJob.position}
                    </h3>
                    <Badge variant="success">Actual</Badge>
                  </div>
                  <p className="text-sm text-sky-600">
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
      <section className="border-t border-blue-100">
        <div className="relative overflow-hidden bg-gradient-to-br from-sky-600 via-sky-700 to-blue-800 py-20">
          {/* Blobs decorativos */}
          <div className="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/5 blur-3xl" />
          <div className="absolute -bottom-20 -left-20 h-72 w-72 rounded-full bg-sky-400/10 blur-3xl" />
          <div className="absolute left-1/2 top-0 h-40 w-96 -translate-x-1/2 rounded-full bg-blue-400/10 blur-2xl" />

          <div className="relative mx-auto max-w-2xl px-4 text-center">
            <h2 className="mb-4 text-2xl font-bold tracking-tight text-white sm:text-3xl">
              ¿Tienes un proyecto en mente?
            </h2>
            <p className="mb-8 text-sky-200">
              Estoy disponible para proyectos freelance, colaboraciones y nuevas
              oportunidades. Hablemos.
            </p>
            <Link
              href="/contacto"
              className="inline-flex items-center gap-2 rounded-lg bg-white px-8 py-3 text-sm font-semibold text-sky-700 shadow-lg transition-all duration-150 hover:bg-sky-50 hover:shadow-xl active:scale-95"
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
