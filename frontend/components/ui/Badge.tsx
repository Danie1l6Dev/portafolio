import { HTMLAttributes } from 'react';
import { cn } from '@/lib/utils';

type BadgeVariant = 'default' | 'primary' | 'success' | 'warning' | 'danger' | 'custom';

interface BadgeProps extends HTMLAttributes<HTMLSpanElement> {
  variant?: BadgeVariant;
  /** Clases personalizadas de color (usadas cuando variant='custom') */
  colorClass?: string;
}

const variantClasses: Record<Exclude<BadgeVariant, 'custom'>, string> = {
  default: 'bg-gray-100 text-gray-700',
  primary: 'bg-indigo-100 text-indigo-700',
  success: 'bg-green-100 text-green-700',
  warning: 'bg-yellow-100 text-yellow-800',
  danger: 'bg-red-100 text-red-700',
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
        'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium',
        color,
        className,
      )}
      {...props}
    >
      {children}
    </span>
  );
}
