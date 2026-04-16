import Link from 'next/link';

export default function HomePage() {
  return (
    <main className="mx-auto max-w-3xl px-4 py-20">
      {/* Hero */}
      <section className="mb-16">
        <p className="mb-2 text-sm font-medium uppercase tracking-widest text-indigo-500">
          Bienvenido
        </p>
        <h1 className="mb-4 text-4xl font-bold leading-tight text-gray-900 sm:text-5xl">
          Hola, soy{' '}
          <span className="text-indigo-600">Daniel Sierra</span>
        </h1>
        <p className="mb-8 max-w-xl text-lg text-gray-500 leading-relaxed">
          Desarrollador de software apasionado por construir productos digitales
          útiles, bien diseñados y fáciles de usar.
        </p>
        <div className="flex flex-wrap gap-3">
          <Link
            href="/proyectos"
            className="rounded-lg bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors"
          >
            Ver proyectos
          </Link>
          <Link
            href="/experiencia"
            className="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors"
          >
            Mi experiencia
          </Link>
        </div>
      </section>

      {/* Links rápidos */}
      <section className="grid gap-4 sm:grid-cols-3">
        {[
          {
            href: '/proyectos',
            title: 'Proyectos',
            desc: 'Trabajos y proyectos personales.',
          },
          {
            href: '/habilidades',
            title: 'Habilidades',
            desc: 'Tecnologías y herramientas.',
          },
          {
            href: '/experiencia',
            title: 'Experiencia',
            desc: 'Trayectoria profesional.',
          },
        ].map(({ href, title, desc }) => (
          <Link
            key={href}
            href={href}
            className="rounded-xl border border-gray-200 p-5 transition-shadow hover:shadow-md"
          >
            <h2 className="font-semibold text-gray-900">{title}</h2>
            <p className="mt-1 text-sm text-gray-400">{desc}</p>
          </Link>
        ))}
      </section>
    </main>
  );
}
