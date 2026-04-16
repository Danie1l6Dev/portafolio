import Link from 'next/link';
import Image from 'next/image';
import { Card, CardBody } from '@/components/ui/Card';
import { Badge } from '@/components/ui/Badge';
import { truncate } from '@/lib/utils';
import type { Project } from '@/types';

interface ProjectCardProps {
  project: Project;
}

export function ProjectCard({ project }: ProjectCardProps) {
  return (
    <Card hover className="flex flex-col overflow-hidden">
      {/* Cover */}
      <div className="relative aspect-video w-full bg-gray-100">
        {project.cover_image ? (
          <Image
            src={project.cover_image}
            alt={project.title}
            fill
            className="object-cover"
            sizes="(max-width: 768px) 100vw, 33vw"
          />
        ) : (
          <div className="flex h-full items-center justify-center text-gray-300 text-4xl">
            ◻
          </div>
        )}
        {project.is_featured && (
          <span className="absolute left-3 top-3">
            <Badge variant="primary">Destacado</Badge>
          </span>
        )}
      </div>

      <CardBody className="flex flex-1 flex-col gap-3">
        {/* Categoría */}
        {project.category && (
          <Badge
            variant="custom"
            colorClass={
              project.category.color
                ? undefined
                : 'bg-gray-100 text-gray-700'
            }
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

        {/* Título */}
        <h3 className="font-semibold text-gray-900 leading-snug">
          {project.title}
        </h3>

        {/* Resumen */}
        <p className="flex-1 text-sm text-gray-500">
          {truncate(project.summary, 110)}
        </p>

        {/* Skills */}
        {project.skills && project.skills.length > 0 && (
          <div className="flex flex-wrap gap-1">
            {project.skills.slice(0, 4).map((skill) => (
              <Badge key={skill.id} variant="default" className="text-xs">
                {skill.name}
              </Badge>
            ))}
            {project.skills.length > 4 && (
              <Badge variant="default" className="text-xs">
                +{project.skills.length - 4}
              </Badge>
            )}
          </div>
        )}

        {/* Acciones */}
        <div className="flex gap-3 pt-1">
          <Link
            href={`/proyectos/${project.slug}`}
            className="text-sm font-medium text-indigo-600 hover:underline"
          >
            Ver proyecto →
          </Link>
          {project.demo_url && (
            <a
              href={project.demo_url}
              target="_blank"
              rel="noopener noreferrer"
              className="text-sm text-gray-500 hover:text-gray-700 hover:underline"
            >
              Demo
            </a>
          )}
          {project.repo_url && (
            <a
              href={project.repo_url}
              target="_blank"
              rel="noopener noreferrer"
              className="text-sm text-gray-500 hover:text-gray-700 hover:underline"
            >
              Código
            </a>
          )}
        </div>
      </CardBody>
    </Card>
  );
}
