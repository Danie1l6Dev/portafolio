import Image from 'next/image';
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
    <article className="flex gap-4">
      {/* Logo */}
      <div className="relative mt-1 h-10 w-10 flex-shrink-0 overflow-hidden rounded-md border border-gray-200 bg-gray-50">
        {experience.company_logo ? (
          <Image
            src={experience.company_logo}
            alt={experience.company}
            fill
            className="object-contain p-1"
            sizes="40px"
          />
        ) : (
          <div className="flex h-full items-center justify-center text-lg font-bold text-gray-300">
            {experience.company.charAt(0).toUpperCase()}
          </div>
        )}
      </div>

      {/* Contenido */}
      <div className="flex-1 border-l border-gray-200 pl-4">
        <p className="text-xs text-gray-400">{dateRange}</p>
        <h3 className="font-semibold text-gray-900">{experience.position}</h3>
        <p className="text-sm text-indigo-600">
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
            <span className="text-gray-400"> · {experience.location}</span>
          )}
        </p>
        {experience.description && (
          <p className="mt-2 text-sm text-gray-600 leading-relaxed">
            {experience.description}
          </p>
        )}
        {experience.is_current && (
          <span className="mt-2 inline-block rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700">
            Actual
          </span>
        )}
      </div>
    </article>
  );
}
