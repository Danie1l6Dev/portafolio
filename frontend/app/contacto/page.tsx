import type { Metadata } from 'next';
import { ContactForm } from '@/components/portfolio/ContactForm';

export const metadata: Metadata = {
  title: 'Contacto',
  description: 'Envíame un mensaje, respondo a la mayor brevedad posible.',
};

export default function ContactoPage() {
  return (
    <main className="mx-auto max-w-2xl px-4 py-10">
      <h1 className="mb-2 text-3xl font-bold text-gray-900">Contacto</h1>
      <p className="mb-8 text-gray-500">
        ¿Tienes un proyecto en mente o quieres ponerte en contacto? Escríbeme y
        te respondo a la mayor brevedad.
      </p>

      <ContactForm />

      {/* Canales alternativos */}
      <div className="mt-10 border-t border-gray-100 pt-8">
        <p className="mb-4 text-sm font-medium text-gray-400 uppercase tracking-wider">
          También puedes encontrarme en
        </p>
        <div className="flex flex-wrap gap-4">
          {/* Personaliza estos enlaces en Fase 8 con datos reales */}
          <a
            href="https://github.com"
            target="_blank"
            rel="noopener noreferrer"
            className="text-sm text-gray-600 hover:text-indigo-600 transition-colors"
          >
            GitHub →
          </a>
          <a
            href="https://linkedin.com"
            target="_blank"
            rel="noopener noreferrer"
            className="text-sm text-gray-600 hover:text-indigo-600 transition-colors"
          >
            LinkedIn →
          </a>
        </div>
      </div>
    </main>
  );
}
