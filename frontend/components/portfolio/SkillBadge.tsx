import Image from 'next/image';
import { cn, skillLevelLabel } from '@/lib/utils';
import type { Skill } from '@/types';

interface SkillBadgeProps {
  skill: Skill;
  showLevel?: boolean;
}

// ── Icono ─────────────────────────────────────────────────────

/**
 * Si el icono tiene el prefijo "si:" se renderiza desde Simple Icons CDN.
 * De lo contrario se trata como emoji o texto plano.
 */
function SkillIcon({ icon, name }: { icon: string; name: string }) {
  if (icon.startsWith('si:')) {
    const slug = icon.slice(3);
    return (
      <span className="flex h-6 w-6 items-center justify-center rounded-md bg-slate-50" aria-hidden>
        <Image
          src={`https://cdn.simpleicons.org/${slug}`}
          alt={name}
          width={16}
          height={16}
          className="h-4 w-4 object-contain"
          unoptimized
        />
      </span>
    );
  }

  return (
    <span className="flex h-6 w-6 items-center justify-center rounded-md bg-slate-50 text-sm leading-none" aria-hidden>
      {icon}
    </span>
  );
}

// ── Puntos de nivel ───────────────────────────────────────────

function LevelDots({ level }: { level: number }) {
  return (
    <span className="flex gap-0.5" aria-label={`Nivel ${level} de 5`}>
      {Array.from({ length: 5 }).map((_, i) => (
        <span
          key={i}
          className={cn(
            'h-1.5 w-1.5 rounded-full transition-colors',
            i < level ? 'bg-sky-500' : 'bg-slate-200',
          )}
        />
      ))}
    </span>
  );
}

// ── Badge ─────────────────────────────────────────────────────

export function SkillBadge({ skill, showLevel = false }: SkillBadgeProps) {
  return (
    <div className="flex flex-col items-start gap-1.5 rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 shadow-[0_1px_2px_0_rgb(0_0_0_/_0.05)] transition-all duration-150 hover:-translate-y-0.5 hover:border-sky-200 hover:shadow-[0_4px_8px_-2px_rgb(99_102_241_/_0.15)]">
      <div className="flex items-center gap-2">
        {skill.icon && <SkillIcon icon={skill.icon} name={skill.name} />}
        <span className="text-sm font-semibold text-slate-800">{skill.name}</span>
      </div>
      {showLevel && (
        <div className="flex items-center gap-2">
          <LevelDots level={skill.level} />
          <span className="text-xs text-slate-400">{skillLevelLabel(skill.level)}</span>
        </div>
      )}
    </div>
  );
}
