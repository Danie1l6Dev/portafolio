'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { cn } from '@/lib/utils';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/Button';

const ADMIN_LINKS = [
  { href: '/admin', label: 'Dashboard', exact: true },
  { href: '/admin/proyectos', label: 'Proyectos' },
  { href: '/admin/categorias', label: 'Categorías' },
  { href: '/admin/habilidades', label: 'Habilidades' },
  { href: '/admin/experiencias', label: 'Experiencias' },
];

export function AdminSidebar() {
  const pathname = usePathname();
  const { user, logout, loading } = useAuth();

  return (
    <aside className="flex h-screen w-56 flex-col border-r border-gray-200 bg-white">
      {/* Logo / Título */}
      <div className="border-b border-gray-100 px-4 py-4">
        <Link
          href="/admin"
          className="text-sm font-bold text-gray-900 hover:text-indigo-600 transition-colors"
        >
          Panel Admin
        </Link>
        {user && (
          <p className="mt-0.5 truncate text-xs text-gray-400">{user.email}</p>
        )}
      </div>

      {/* Navegación */}
      <nav className="flex-1 overflow-y-auto px-2 py-3">
        <ul className="space-y-0.5">
          {ADMIN_LINKS.map(({ href, label, exact }) => {
            const isActive = exact
              ? pathname === href
              : pathname.startsWith(href);

            return (
              <li key={href}>
                <Link
                  href={href}
                  className={cn(
                    'flex items-center rounded-md px-3 py-2 text-sm font-medium transition-colors',
                    isActive
                      ? 'bg-indigo-50 text-indigo-600'
                      : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900',
                  )}
                >
                  {label}
                </Link>
              </li>
            );
          })}
        </ul>
      </nav>

      {/* Footer del sidebar */}
      <div className="border-t border-gray-100 px-4 py-3 space-y-2">
        <Link
          href="/"
          className="block text-xs text-gray-400 hover:text-gray-700 transition-colors"
          target="_blank"
        >
          Ver portafolio →
        </Link>
        <Button
          variant="ghost"
          size="sm"
          onClick={logout}
          loading={loading}
          className="w-full justify-start text-red-500 hover:bg-red-50 hover:text-red-600"
        >
          Cerrar sesión
        </Button>
      </div>
    </aside>
  );
}
