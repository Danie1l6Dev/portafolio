import { notFound } from 'next/navigation';
import Image from 'next/image';
import Link from 'next/link';
import type { Metadata } from 'next';
import { getProjects, getProject } from '@/services/projects';
import { Badge } from '@/components/ui/Badge';
import { SkillBadge } from '@/components/portfolio/SkillBadge';
import { formatDateRange } from '@/lib/utils';
import type { Project } from '@/types';

interface Props {
  params: { slug: string };
}

export const revalidate = 60;

// ── Metadata dinámica ─────────────────────────────────────────

export async function generateMetadata({ params }: Props): Promise<Metadata> {
  try {
    const { data } = await getProjects();
    const found = data.find((p) => p.slug === params.slug);
    if (!found) return {};
    return {
      title: found.title,
      description: found.summary,
      openGraph: { images: found.cover_image ? [found.cover_image] : [] },
    };
  } catch {
    return {};
  }
}

export async function generateStaticParams() {
  try {
    const { data } = await getProjects({ page: 1 });
    return data.map((p) => ({ slug: p.slug }));
  } catch {
    return [];
  }
}

// ── Página ────────────────────────────────────────────────────

export default async function ProjectDetailPage({ params }: Props) {
  let project: Project;

  try {
    const { data } = await getProjects();
    const found = data.find((p) => p.slug === params.slug);
    if (!found) notFound();
    project = await getProject(found.id);
  } catch {
    notFound();
  }

  const dateRange = formatDateRange(
    project.started_at,
    project.finished_at,
    project.in_progress,
  );

  const images = project.media?.filter((m) => m.is_image) ?? [];

  return (
    <main className="mx-auto max-w-3xl px-4 py-10">
      {/* Breadcrumb */}
      <nav className="mb-6 flex items-center gap-2 text-sm text-gray-400">
        <Link href="/proyectos" className="hover:text-gray-700 transition-colors">
          Proyectos
        </Link>
        <span>/</span>
        <span className="text-gray-600">{project.title}</span>
      </nav>

      {/* Cover principal */}
      {project.cover_image && (
        <div className="relative mb-8 aspect-video w-full overflow-hidden rounded-2xl bg-gray-100 shadow-sm">
          <Image
            src={project.cover_image}
            alt={project.title}
            fill
            className="object-cover"
            priority
            sizes="(max-width: 768px) 100vw, 768px"
          />
        </div>
      )}

      {/* Encabezado */}
      <div className="mb-6">
        <div className="mb-3 flex flex-wrap items-center gap-2">
          {project.category && (
            <Badge
              variant="custom"
              colorClass={project.category.color ? undefined : 'bg-indigo-100 text-indigo-700'}
              style={
                project.category.color
                  ? {
                      backgroundColor: `${project.category.color}22`,
                      color: project.category.color,
                    }
                  : undefined
              }
            >
              {project.category.name}
            </Badge>
          )}
          {project.is_featured && <Badge variant="primary">Destacado</Badge>}
          {project.in_progress && <Badge variant="warning">En progreso</Badge>}
        </div>

        <h1 className="mb-2 text-3xl font-bold text-gray-900 sm:text-4xl">
          {project.title}
        </h1>

        <p className="text-sm text-gray-400">{dateRange}</p>
      </div>

      {/* Botones de acción */}
      {(project.demo_url || project.repo_url) && (
        <div className="mb-8 flex flex-wrap gap-3">
          {project.demo_url && (
            <a
              href={project.demo_url}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors"
            >
              Ver demo →
            </a>
          )}
          {project.repo_url && (
            <a
              href={project.repo_url}
              target="_blank"
              rel="noopener noreferrer"
              className="inline-flex items-center gap-2 rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
            >
              Ver código
            </a>
          )}
        </div>
      )}

      {/* Resumen */}
      <p className="mb-8 text-lg text-gray-600 leading-relaxed border-l-4 border-indigo-200 pl-4 italic">
        {project.summary}
      </p>

      {/* Descripción completa */}
      {project.description && (
        <section className="mb-10">
          <h2 className="mb-4 text-lg font-semibold text-gray-900">
            Sobre el proyecto
          </h2>
          <div className="text-gray-700 leading-relaxed whitespace-pre-line">
            {project.description}
          </div>
        </section>
      )}

      {/* Habilidades / tecnologías */}
      {project.skills && project.skills.length > 0 && (
        <section className="mb-10">
          <h2 className="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-400">
            Tecnologías utilizadas
          </h2>
          <div className="flex flex-wrap gap-2">
            {project.skills.map((skill) => (
              <SkillBadge key={skill.id} skill={skill} />
            ))}
          </div>
        </section>
      )}

      {/* Galería de imágenes */}
      {images.length > 0 && (
        <section className="mb-10">
          <h2 className="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-400">
            Galería
          </h2>
          <div className="grid gap-3 sm:grid-cols-2">
            {images.map((img) => (
              <a
                key={img.id}
                href={img.url}
                target="_blank"
                rel="noopener noreferrer"
                className="group relative aspect-video overflow-hidden rounded-xl bg-gray-100"
              >
                <Image
                  src={img.url}
                  alt={img.alt ?? project.title}
                  fill
                  className="object-cover transition-transform duration-300 group-hover:scale-105"
                  sizes="(max-width: 640px) 100vw, 50vw"
                />
              </a>
            ))}
          </div>
        </section>
      )}

      {/* Volver */}
      <div className="border-t border-gray-100 pt-8">
        <Link
          href="/proyectos"
          className="text-sm font-medium text-indigo-600 hover:underline"
        >
          ← Volver a proyectos
        </Link>
      </div>
    </main>
  );
}
