export default function AdminDashboardPage() {
  return (
    <div>
      <h1 className="mb-1 text-2xl font-bold text-gray-900">Dashboard</h1>
      <p className="text-sm text-gray-400">
        Bienvenido al panel de administración del portafolio.
      </p>

      <div className="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {[
          { label: 'Proyectos', href: '/admin/proyectos' },
          { label: 'Categorías', href: '/admin/categorias' },
          { label: 'Habilidades', href: '/admin/habilidades' },
          { label: 'Experiencias', href: '/admin/experiencias' },
        ].map(({ label, href }) => (
          <a
            key={href}
            href={href}
            className="rounded-xl border border-gray-200 bg-white p-6 shadow-sm transition-shadow hover:shadow-md"
          >
            <p className="text-sm font-medium text-gray-500">Gestionar</p>
            <p className="mt-1 text-lg font-semibold text-gray-900">{label}</p>
          </a>
        ))}
      </div>
    </div>
  );
}
