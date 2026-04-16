import type { Metadata } from 'next';
import { ContactForm } from '@/components/portfolio/ContactForm';

export const metadata: Metadata = {
  title: 'Contacto — Daniel Sierra',
  description: 'Envíame un mensaje, respondo a la mayor brevedad posible.',
};

export default function ContactoPage() {
  return (
    <main className="mx-auto max-w-5xl px-4 py-14">
      {/* Header */}
      <div className="mb-12">
        <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-indigo-500">
          Hablemos
        </p>
        <h1 className="mb-3 text-3xl font-bold tracking-tight text-slate-900">
          Contacto
        </h1>
        <p className="text-slate-500">
          ¿Tienes un proyecto en mente o quieres ponerte en contacto? Escríbeme.
        </p>
      </div>

      <div className="grid grid-cols-1 gap-12 lg:grid-cols-5">
        {/* Form — 3/5 */}
        <div className="lg:col-span-3">
          <ContactForm />
        </div>

        {/* Info panel — 2/5 */}
        <aside className="lg:col-span-2">
          <div className="rounded-2xl border border-slate-200 bg-slate-50 p-6">
            {/* Disponibilidad */}
            <div className="mb-6">
              <p className="mb-1 flex items-center gap-2 text-sm font-semibold text-slate-900">
                <span className="h-2 w-2 animate-pulse rounded-full bg-emerald-500" />
                Disponible
              </p>
              <p className="text-sm text-slate-500">
                Actualmente acepto proyectos freelance y colaboraciones.
              </p>
            </div>

            <div className="space-y-5">
              <div>
                <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-slate-400">
                  Correo
                </p>
                <a
                  href="mailto:desarrollomaicao@uniguajira.edu.co"
                  className="text-sm text-indigo-600 hover:underline"
                >
                  desarrollomaicao@uniguajira.edu.co
                </a>
              </div>

              <div className="h-px bg-slate-200" />

              <div>
                <p className="mb-2 text-xs font-semibold uppercase tracking-widest text-slate-400">
                  Redes
                </p>
                <div className="flex flex-col gap-1.5">
                  <a
                    href="https://github.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-sm text-slate-600 transition-colors hover:text-indigo-600"
                  >
                    GitHub →
                  </a>
                  <a
                    href="https://linkedin.com"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-sm text-slate-600 transition-colors hover:text-indigo-600"
                  >
                    LinkedIn →
                  </a>
                </div>
              </div>

              <div className="h-px bg-slate-200" />

              <div>
                <p className="mb-1 text-xs font-semibold uppercase tracking-widest text-slate-400">
                  Respuesta
                </p>
                <p className="text-sm text-slate-500">
                  Generalmente respondo en menos de 48 horas.
                </p>
              </div>
            </div>
          </div>
        </aside>
      </div>
    </main>
  );
}
