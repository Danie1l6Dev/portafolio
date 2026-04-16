import { cn, skillLevelLabel } from '@/lib/utils';
import type { Skill } from '@/types';

interface SkillBadgeProps {
  skill: Skill;
  showLevel?: boolean;
}

/** Rellena N de 5 barras según el nivel de la habilidad */
function LevelBars({ level }: { level: number }) {
  return (
    <span className="flex gap-0.5" aria-label={`Nivel ${level} de 5`}>
      {Array.from({ length: 5 }).map((_, i) => (
        <span
          key={i}
          className={cn(
            'h-1.5 w-2 rounded-full',
            i < level ? 'bg-indigo-500' : 'bg-gray-200',
          )}
        />
      ))}
    </span>
  );
}

export function SkillBadge({ skill, showLevel = false }: SkillBadgeProps) {
  return (
    <div className="flex flex-col items-start gap-1 rounded-lg border border-gray-200 bg-white px-3 py-2 shadow-sm">
      <div className="flex items-center gap-2">
        {skill.icon && (
          <span className="text-base leading-none" aria-hidden>
            {skill.icon}
          </span>
        )}
        <span className="text-sm font-medium text-gray-800">{skill.name}</span>
      </div>
      {showLevel && (
        <div className="flex items-center gap-2">
          <LevelBars level={skill.level} />
          <span className="text-xs text-gray-400">{skillLevelLabel(skill.level)}</span>
        </div>
      )}
    </div>
  );
}
