import Link from 'next/link';
import { NAV_LINKS, SITE, SOCIAL_LINKS } from '@/lib/constants';

export function Footer() {
  const year = new Date().getFullYear();

  return (
    <footer className="border-t border-slate-100 bg-white">
      <div className="mx-auto max-w-5xl px-4 py-10">
        <div className="grid grid-cols-1 gap-8 sm:grid-cols-3">
          {/* Marca */}
          <div>
            <div className="mb-3 flex items-center gap-2">
              <span className="flex h-7 w-7 items-center justify-center rounded-md bg-indigo-600 text-xs font-black text-white">
                DS
              </span>
              <span className="font-bold text-slate-900">{SITE.author}</span>
            </div>
            <p className="text-sm text-slate-400 leading-relaxed">
              {SITE.description}
            </p>
          </div>

          {/* Navegación */}
          <div>
            <p className="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-400">
              Navegación
            </p>
            <ul className="space-y-1.5">
              {NAV_LINKS.map(({ href, label }) => (
                <li key={href}>
                  <Link
                    href={href}
                    className="text-sm text-slate-500 transition-colors hover:text-indigo-600"
                  >
                    {label}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Contacto */}
          <div>
            <p className="mb-3 text-xs font-semibold uppercase tracking-widest text-slate-400">
              Conectar
            </p>
            <ul className="space-y-1.5">
              {SOCIAL_LINKS.map(({ href, label, rel }) => (
                <li key={href}>
                  <a
                    href={href}
                    target="_blank"
                    rel={rel}
                    className="text-sm text-slate-500 transition-colors hover:text-indigo-600"
                  >
                    {label} →
                  </a>
                </li>
              ))}
              <li>
                <Link
                  href="/contacto"
                  className="text-sm text-slate-500 transition-colors hover:text-indigo-600"
                >
                  Enviar mensaje →
                </Link>
              </li>
            </ul>
          </div>
        </div>

        <div className="mt-8 border-t border-slate-100 pt-6 text-center text-xs text-slate-400">
          © {year} Daniel Sierra. Todos los derechos reservados.
        </div>
      </div>
    </footer>
  );
}
