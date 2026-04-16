import { notFound } from 'next/navigation';
import Image from 'next/image';
import Link from 'next/link';
import { getProjects, getProject } from '@/services/projects';
import { Badge } from '@/components/ui/Badge';
import { SkillBadge } from '@/components/portfolio/SkillBadge';
import { formatDate, formatDateRange } from '@/lib/utils';

interface Props {
  params: { slug: string };
}

// ISR: regenerar cada 60 segundos
export const revalidate = 60;

export async function generateStaticParams() {
  try {
    const { data } = await getProjects({ page: 1 });
    return data.map((p) => ({ slug: p.slug }));
  } catch {
    return [];
  }
}

export default async function ProjectDetailPage({ params }: Props) {
  // Buscamos por slug usando la lista pública (la API show usa ID)
  let project;
  try {
    const { data } = await getProjects();
    const found = data.find((p) => p.slug === params.slug);
    if (!found) notFound();
    project = await getProject(found.id);
  } catch {
    notFound();
  }

  return (
    <main className="mx-auto max-w-3xl px-4 py-10">
      {/* Breadcrumb */}
      <nav className="mb-6 text-sm text-gray-400">
        <Link href="/proyectos" className="hover:text-gray-700">
          Proyectos
        </Link>{' '}
        / <span className="text-gray-600">{project.title}</span>
      </nav>

      {/* Cover */}
      {project.cover_image && (
        <div className="relative mb-8 aspect-video w-full overflow-hidden rounded-xl bg-gray-100">
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
      <div className="mb-6 flex flex-wrap items-start justify-between gap-4">
        <div>
          {project.category && (
            <Badge variant="primary" className="mb-2">
              {project.category.name}
            </Badge>
          )}
          <h1 className="text-3xl font-bold text-gray-900">{project.title}</h1>
          <p className="mt-1 text-sm text-gray-400">
            {formatDateRange(
              project.started_at,
              project.finished_at,
              project.in_progress,
            )}
          </p>
        </div>

        <div className="flex gap-3">
          {project.demo_url && (
            <a
              href={project.demo_url}
              target="_blank"
              rel="noopener noreferrer"
              className="rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
            >
              Demo
            </a>
          )}
          {project.repo_url && (
            <a
              href={project.repo_url}
              target="_blank"
              rel="noopener noreferrer"
              className="rounded-md border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
            >
              Código
            </a>
          )}
        </div>
      </div>

      {/* Resumen */}
      <p className="mb-6 text-lg text-gray-600 leading-relaxed">
        {project.summary}
      </p>

      {/* Descripción */}
      {project.description && (
        <div className="prose prose-gray max-w-none mb-8">
          <p className="whitespace-pre-line text-gray-700 leading-relaxed">
            {project.description}
          </p>
        </div>
      )}

      {/* Habilidades */}
      {project.skills && project.skills.length > 0 && (
        <section className="mb-8">
          <h2 className="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-400">
            Tecnologías
          </h2>
          <div className="flex flex-wrap gap-2">
            {project.skills.map((skill) => (
              <SkillBadge key={skill.id} skill={skill} />
            ))}
          </div>
        </section>
      )}
    </main>
  );
}
