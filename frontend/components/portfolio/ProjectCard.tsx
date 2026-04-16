import Link from 'next/link';
import Image from 'next/image';
import { Badge } from '@/components/ui/Badge';
import { truncate } from '@/lib/utils';
import type { Project } from '@/types';

interface ProjectCardProps {
  project: Project;
}

export function ProjectCard({ project }: ProjectCardProps) {
  return (
    <article className="group flex flex-col overflow-hidden rounded-xl border border-slate-200 bg-white shadow-[0_1px_3px_0_rgb(0_0_0_/_0.07)] transition-all duration-200 hover:-translate-y-1 hover:shadow-[0_8px_20px_-4px_rgb(0_0_0_/_0.12)]">
      {/* Cover */}
      <div className="relative aspect-video w-full overflow-hidden bg-slate-100">
        {project.cover_image ? (
          <Image
            src={project.cover_image}
            alt={project.title}
            fill
            className="object-cover transition-transform duration-300 group-hover:scale-105"
            sizes="(max-width: 768px) 100vw, 33vw"
          />
        ) : (
          <div className="flex h-full items-center justify-center text-slate-300">
            <svg width="40" height="40" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5">
              <rect x="2" y="3" width="20" height="14" rx="2"/>
              <path d="M8 21h8M12 17v4"/>
            </svg>
          </div>
        )}

        {/* Overlay gradient on hover */}
        <div className="absolute inset-0 bg-gradient-to-t from-slate-900/30 to-transparent opacity-0 transition-opacity duration-200 group-hover:opacity-100" />

        {project.is_featured && (
          <span className="absolute left-3 top-3 z-10">
            <Badge variant="primary">Destacado</Badge>
          </span>
        )}

        {/* Hover actions overlay */}
        <div className="absolute inset-x-0 bottom-0 z-10 flex gap-2 p-3 opacity-0 transition-opacity duration-200 group-hover:opacity-100">
          {project.demo_url && (
            <a
              href={project.demo_url}
              target="_blank"
              rel="noopener noreferrer"
              onClick={(e) => e.stopPropagation()}
              className="rounded-md bg-white/90 px-2.5 py-1 text-xs font-semibold text-slate-800 backdrop-blur-sm transition-colors hover:bg-white"
            >
              Demo →
            </a>
          )}
          {project.repo_url && (
            <a
              href={project.repo_url}
              target="_blank"
              rel="noopener noreferrer"
              onClick={(e) => e.stopPropagation()}
              className="rounded-md bg-white/90 px-2.5 py-1 text-xs font-semibold text-slate-800 backdrop-blur-sm transition-colors hover:bg-white"
            >
              Código →
            </a>
          )}
        </div>
      </div>

      {/* Body */}
      <div className="flex flex-1 flex-col gap-3 px-5 py-4">
        {/* Categoría */}
        {project.category && (
          <Badge
            variant="custom"
            colorClass={
              project.category.color
                ? undefined
                : 'bg-slate-100 text-slate-600'
            }
            style={
              project.category.color
                ? {
                    backgroundColor: `${project.category.color}18`,
                    color: project.category.color,
                  }
                : undefined
            }
          >
            {project.category.name}
          </Badge>
        )}

        {/* Título */}
        <h3 className="font-semibold leading-snug text-slate-900 transition-colors group-hover:text-indigo-600">
          {project.title}
        </h3>

        {/* Resumen */}
        <p className="flex-1 text-sm leading-relaxed text-slate-500">
          {truncate(project.summary, 110)}
        </p>

        {/* Skills */}
        {project.skills && project.skills.length > 0 && (
          <div className="flex flex-wrap gap-1">
            {project.skills.slice(0, 4).map((skill) => (
              <Badge key={skill.id} variant="default" className="text-xs">
                {skill.icon && <span className="mr-1">{skill.icon}</span>}
                {skill.name}
              </Badge>
            ))}
            {project.skills.length > 4 && (
              <Badge variant="default" className="text-xs text-slate-400">
                +{project.skills.length - 4}
              </Badge>
            )}
          </div>
        )}

        {/* Link */}
        <div className="pt-1">
          <Link
            href={`/proyectos/${project.slug}`}
            className="inline-flex items-center gap-1 text-sm font-medium text-indigo-600 transition-colors hover:text-indigo-800"
          >
            Ver proyecto
            <svg className="h-3.5 w-3.5 transition-transform duration-150 group-hover:translate-x-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
              <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
            </svg>
          </Link>
        </div>
      </div>
    </article>
  );
}
