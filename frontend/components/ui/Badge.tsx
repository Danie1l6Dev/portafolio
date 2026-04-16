import { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

type BadgeVariant = 'default' | 'primary' | 'success' | 'warning' | 'danger' | 'custom';

interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
  variant?: BadgeVariant;
  /** Clases personalizadas de color (usadas cuando variant='custom') */
  colorClass?: string;
}

const variantClasses: Record<Exclude<BadgeVariant, 'custom'>, string> = {
  default: 'bg-slate-100 text-slate-600',
  primary: 'bg-indigo-50 text-indigo-700 ring-1 ring-indigo-100',
  success: 'bg-emerald-50 text-emerald-700 ring-1 ring-emerald-100',
  warning: 'bg-amber-50 text-amber-700 ring-1 ring-amber-100',
  danger:  'bg-red-50 text-red-700 ring-1 ring-red-100',
};

export function Badge({
  variant = 'default',
  colorClass,
  className,
  children,
  ...props
}: BadgeProps) {
  const color =
    variant === 'custom' && colorClass
      ? colorClass
      : variantClasses[variant as Exclude<BadgeVariant, 'custom'>];

  return (
    <span
      className={cn(
        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium tracking-wide',
        color,
        className,
      )}
      {...props}
    >
      {children}
    </span>
  );
}
