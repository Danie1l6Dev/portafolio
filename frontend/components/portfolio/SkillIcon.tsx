'use client';

import Image from 'next/image';
import { cn } from '@/lib/utils';

interface SkillIconProps {
  icon: string;
  name: string;
  size?: 'sm' | 'md';
  className?: string;
}

export function SkillIcon({ icon, name, size = 'md', className }: SkillIconProps) {
  const isSmall = size === 'sm';
  const wrapperSize = isSmall ? 'h-4 w-4' : 'h-6 w-6';
  const imageSize = isSmall ? 12 : 16;
  const textSize = isSmall ? 'text-xs' : 'text-sm';

  if (icon.startsWith('si:')) {
    const slug = icon.slice(3);
    return (
      <span className={cn('inline-flex items-center justify-center rounded bg-slate-50', wrapperSize, className)} aria-hidden>
        <Image
          src={`https://cdn.simpleicons.org/${slug}`}
          alt={name}
          width={imageSize}
          height={imageSize}
          className="object-contain"
          unoptimized
        />
      </span>
    );
  }

  return (
    <span className={cn('inline-flex items-center justify-center rounded bg-slate-50 leading-none', wrapperSize, textSize, className)} aria-hidden>
      {icon}
    </span>
  );
}

