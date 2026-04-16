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

// Revalidar cada 5 minutos
export const revalidate = 300;

export default async function HomePage() {
  // Fetch en paralelo — si falla no rompe la página
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
      <section className="mx-auto max-w-5xl px-4 py-20 sm:py-28">
        <div className="max-w-2xl">
          {currentJob && (
            <p className="mb-4 inline-flex items-center gap-2 rounded-full bg-green-50 px-3 py-1 text-sm text-green-700">
              <span className="h-2 w-2 rounded-full bg-green-500 animate-pulse" />
              {currentJob.position} en {currentJob.company}
            </p>
          )}

          <h1 className="mb-5 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl lg:text-6xl">
            Hola, soy{' '}
            <span className="text-indigo-600">Daniel Sierra</span>
          </h1>

          <p className="mb-8 text-lg text-gray-500 leading-relaxed sm:text-xl">
            Desarrollador de software apasionado por construir productos digitales
            útiles, bien diseñados y fáciles de usar. Me especializo en
            aplicaciones web modernas con tecnologías actuales.
          </p>

          <div className="flex flex-wrap gap-3">
            <Link
              href="/proyectos"
              className="rounded-lg bg-indigo-600 px-6 py-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
            >
              Ver proyectos
            </Link>
            <Link
              href="/contacto"
              className="rounded-lg border border-gray-300 px-6 py-3 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Contactar
            </Link>
          </div>
        </div>
      </section>

      {/* ── Proyectos destacados ──────────────────────────── */}
      {featuredProjects.length > 0 && (
        <section className="border-t border-gray-100 py-16">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-8 flex items-end justify-between">
              <div>
                <p className="mb-1 text-xs font-semibold uppercase tracking-widest text-indigo-500">
                  Trabajo reciente
                </p>
                <h2 className="text-2xl font-bold text-gray-900">
                  Proyectos destacados
                </h2>
              </div>
              <Link
                href="/proyectos"
                className="text-sm font-medium text-indigo-600 hover:underline"
              >
                Ver todos →
              </Link>
            </div>

            <div className="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
              {featuredProjects.map((project) => (
                <ProjectCard key={project.id} project={project} />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* ── Habilidades destacadas ────────────────────────── */}
      {featuredSkills.length > 0 && (
        <section className="border-t border-gray-100 bg-gray-50 py-16">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-8 flex items-end justify-between">
              <div>
                <p className="mb-1 text-xs font-semibold uppercase tracking-widest text-indigo-500">
                  Stack técnico
                </p>
                <h2 className="text-2xl font-bold text-gray-900">
                  Habilidades principales
                </h2>
              </div>
              <Link
                href="/habilidades"
                className="text-sm font-medium text-indigo-600 hover:underline"
              >
                Ver todas →
              </Link>
            </div>

            <div className="flex flex-wrap gap-2">
              {featuredSkills.map((skill) => (
                <SkillBadge key={skill.id} skill={skill} showLevel />
              ))}
            </div>
          </div>
        </section>
      )}

      {/* ── Experiencia actual ────────────────────────────── */}
      {currentJob && (
        <section className="border-t border-gray-100 py-16">
          <div className="mx-auto max-w-5xl px-4">
            <div className="mb-8 flex items-end justify-between">
              <div>
                <p className="mb-1 text-xs font-semibold uppercase tracking-widest text-indigo-500">
                  Actualmente
                </p>
                <h2 className="text-2xl font-bold text-gray-900">
                  Experiencia
                </h2>
              </div>
              <Link
                href="/experiencia"
                className="text-sm font-medium text-indigo-600 hover:underline"
              >
                Ver trayectoria →
              </Link>
            </div>

            <div className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
              <div className="flex items-start gap-4">
                {/* Logo */}
                <div className="relative h-12 w-12 flex-shrink-0 overflow-hidden rounded-lg border border-gray-200 bg-gray-50">
                  {currentJob.company_logo ? (
                    <Image
                      src={currentJob.company_logo}
                      alt={currentJob.company}
                      fill
                      className="object-contain p-1.5"
                      sizes="48px"
                    />
                  ) : (
                    <div className="flex h-full items-center justify-center text-xl font-bold text-gray-300">
                      {currentJob.company.charAt(0).toUpperCase()}
                    </div>
                  )}
                </div>
                {/* Info */}
                <div>
                  <div className="flex items-center gap-2">
                    <h3 className="font-semibold text-gray-900">
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
                      <span className="text-gray-400"> · {currentJob.location}</span>
                    )}
                  </p>
                  <p className="mt-0.5 text-xs text-gray-400">
                    {formatDateRange(
                      currentJob.started_at,
                      currentJob.finished_at,
                      currentJob.is_current,
                    )}
                  </p>
                  {currentJob.description && (
                    <p className="mt-3 text-sm text-gray-600 leading-relaxed">
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
      <section className="border-t border-gray-100 bg-indigo-600 py-16">
        <div className="mx-auto max-w-2xl px-4 text-center">
          <h2 className="mb-3 text-2xl font-bold text-white sm:text-3xl">
            ¿Tienes un proyecto en mente?
          </h2>
          <p className="mb-8 text-indigo-200">
            Estoy disponible para proyectos freelance, colaboraciones y nuevas
            oportunidades. Hablemos.
          </p>
          <Link
            href="/contacto"
            className="inline-block rounded-lg bg-white px-8 py-3 text-sm font-semibold text-indigo-700 shadow hover:bg-indigo-50 transition-colors"
          >
            Enviar mensaje
          </Link>
        </div>
      </section>
    </main>
  );
}
