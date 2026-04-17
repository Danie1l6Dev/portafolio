import Image from 'next/image';
import { Badge } from '@/components/ui/Badge';
import { formatDateRange } from '@/lib/utils';
import type { Experience } from '@/types';

interface ExperienceItemProps {
  experience: Experience;
}

export function ExperienceItem({ experience }: ExperienceItemProps) {
  const dateRange = formatDateRange(
    experience.started_at,
    experience.finished_at,
    experience.is_current,
  );

  return (
    <article className="relative flex gap-5">
      {/* Timeline dot + line (left) */}
      <div className="relative flex flex-col items-center">
        <div
          className={`z-10 flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full border-2 ${
            experience.is_current
              ? 'border-emerald-400 bg-white'
              : 'border-slate-200 bg-white'
          }`}
        >
          {experience.company_logo ? (
            <div className="relative h-6 w-6 overflow-hidden rounded-full">
              <Image
                src={experience.company_logo}
                alt={experience.company}
                fill
                className="object-contain"
                sizes="24px"
              />
            </div>
          ) : (
            <span
              className={`text-sm font-bold ${
                experience.is_current ? 'text-emerald-500' : 'text-slate-400'
              }`}
            >
              {experience.company.charAt(0).toUpperCase()}
            </span>
          )}
        </div>
        {/* Connecting line (rendered by parent space-y context) */}
        <div className="mt-2 w-px flex-1 bg-slate-100" />
      </div>

      {/* Contenido */}
      <div className="flex-1 pb-8">
        <div className="mb-0.5 flex flex-wrap items-center gap-2">
          <h3 className="font-semibold text-slate-900">{experience.position}</h3>
          {experience.is_current && <Badge variant="success">Actual</Badge>}
        </div>

        <p className="text-sm text-sky-600">
          {experience.company_url ? (
            <a
              href={experience.company_url}
              target="_blank"
              rel="noopener noreferrer"
              className="hover:underline"
            >
              {experience.company}
            </a>
          ) : (
            experience.company
          )}
          {experience.location && (
            <span className="text-slate-400"> · {experience.location}</span>
          )}
        </p>

        <p className="mt-0.5 text-xs text-slate-400">{dateRange}</p>

        {experience.description && (
          <p className="mt-3 max-w-prose text-sm leading-relaxed text-slate-600">
            {experience.description}
          </p>
        )}
      </div>
    </article>
  );
}
