'use client';

import Link from 'next/link';
import { usePathname } from 'next/navigation';
import { useState, useEffect } from 'react';
import { cn } from '@/lib/utils';
import { NAV_LINKS, SITE } from '@/lib/constants';

export function Navbar() {
  const pathname = usePathname();
  const [open, setOpen] = useState(false);
  const [scrolled, setScrolled] = useState(false);

  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8);
    window.addEventListener('scroll', onScroll, { passive: true });
    return () => window.removeEventListener('scroll', onScroll);
  }, []);

  // Cierra el menú al cambiar de ruta
  useEffect(() => { setOpen(false); }, [pathname]);

  return (
    <header
      className={cn(
        'sticky top-0 z-40 border-b transition-all duration-200',
        scrolled
          ? 'border-slate-200 bg-white/90 shadow-sm backdrop-blur-md'
          : 'border-transparent bg-white/70 backdrop-blur-sm',
      )}
    >
      <nav className="mx-auto flex max-w-5xl items-center justify-between px-4 py-3">
        {/* Logo */}
        <Link
          href="/"
          className="group flex items-center gap-2 font-bold tracking-tight text-slate-900 transition-colors hover:text-indigo-600"
        >
          <span className="flex h-7 w-7 items-center justify-center rounded-md bg-indigo-600 text-xs font-black text-white shadow-sm transition-transform group-hover:scale-105">
            DS
          </span>
          <span className="hidden sm:inline">{SITE.author}</span>
        </Link>

        {/* Desktop nav */}
        <ul className="hidden items-center gap-0.5 md:flex">
          {NAV_LINKS.map(({ href, label }) => {
            const isActive =
              href === '/' ? pathname === '/' : pathname.startsWith(href);
            return (
              <li key={href}>
                <Link
                  href={href}
                  className={cn(
                    'relative rounded-md px-3 py-2 text-sm font-medium transition-colors',
                    isActive
                      ? 'text-indigo-600'
                      : 'text-slate-500 hover:text-slate-900',
                  )}
                >
                  {label}
                  {isActive && (
                    <span className="absolute inset-x-2 -bottom-[13px] h-0.5 rounded-full bg-indigo-500" />
                  )}
                </Link>
              </li>
            );
          })}
        </ul>

        {/* Mobile hamburger */}
        <button
          type="button"
          onClick={() => setOpen((o) => !o)}
          aria-label={open ? 'Cerrar menú' : 'Abrir menú'}
          className="flex h-8 w-8 flex-col items-center justify-center gap-1.5 rounded-md text-slate-600 transition-colors hover:bg-slate-100 md:hidden"
        >
          <span
            className={cn(
              'block h-0.5 w-5 rounded-full bg-current transition-all duration-200',
              open && 'translate-y-2 rotate-45',
            )}
          />
          <span
            className={cn(
              'block h-0.5 w-5 rounded-full bg-current transition-all duration-200',
              open && 'opacity-0',
            )}
          />
          <span
            className={cn(
              'block h-0.5 w-5 rounded-full bg-current transition-all duration-200',
              open && '-translate-y-2 -rotate-45',
            )}
          />
        </button>
      </nav>

      {/* Mobile menu */}
      {open && (
        <div className="animate-slide-down border-t border-slate-100 bg-white px-4 py-3 md:hidden">
          <ul className="space-y-0.5">
            {NAV_LINKS.map(({ href, label }) => {
              const isActive =
                href === '/' ? pathname === '/' : pathname.startsWith(href);
              return (
                <li key={href}>
                  <Link
                    href={href}
                    className={cn(
                      'block rounded-md px-3 py-2 text-sm font-medium transition-colors',
                      isActive
                        ? 'bg-indigo-50 text-indigo-600'
                        : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900',
                    )}
                  >
                    {label}
                  </Link>
                </li>
              );
            })}
          </ul>
        </div>
      )}
    </header>
  );
}
