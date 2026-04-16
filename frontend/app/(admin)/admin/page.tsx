'use client';

import Link from 'next/link';
import { useEffect, useState } from 'react';
import { adminGetProjects } from '@/services/projects';
import { adminGetSkills } from '@/services/skills';
import { adminGetExperiences } from '@/services/experiences';
import { adminGetCategories } from '@/services/categories';

interface Stats {
  projects: { total: number; published: number; draft: number };
  skills: number;
  experiences: { total: number; current: number };
  categories: number;
}

const NAV_CARDS = [
  { href: '/admin/proyectos', label: 'Proyectos', desc: 'Crea y gestiona tus proyectos.' },
  { href: '/admin/categorias', label: 'Categorías', desc: 'Organiza proyectos por categoría.' },
  { href: '/admin/habilidades', label: 'Habilidades', desc: 'Tecnologías y herramientas.' },
  { href: '/admin/experiencias', label: 'Experiencias', desc: 'Historial de experiencia laboral.' },
];

export default function AdminDashboardPage() {
  const [stats, setStats] = useState<Stats | null>(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    Promise.allSettled([
      adminGetProjects({ page: 1 }),
      adminGetProjects({ page: 1, status: 'published' }),
      adminGetProjects({ page: 1, status: 'draft' }),
      adminGetSkills(),
      adminGetExperiences({ page: 1 }),
      adminGetCategories({ page: 1 }),
    ]).then(([all, pub, draft, skills, exp, cats]) => {
      setStats({
        projects: {
          total: all.status === 'fulfilled' ? all.value.meta.total : 0,
          published: pub.status === 'fulfilled' ? pub.value.meta.total : 0,
          draft: draft.status === 'fulfilled' ? draft.value.meta.total : 0,
        },
        skills: skills.status === 'fulfilled' ? skills.value.length : 0,
        experiences: {
          total: exp.status === 'fulfilled' ? exp.value.meta.total : 0,
          current: exp.status === 'fulfilled' ? exp.value.data.filter((e) => e.is_current).length : 0,
        },
        categories: cats.status === 'fulfilled' ? cats.value.meta.total : 0,
      });
    }).finally(() => setLoading(false));
  }, []);

  return (
    <div>
      <h1 className="mb-1 text-2xl font-bold text-gray-900">Dashboard</h1>
      <p className="mb-8 text-sm text-gray-400">
        Bienvenido al panel de administración del portafolio.
      </p>

      {/* Stats */}
      <div className="mb-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <StatCard
          label="Proyectos"
          value={loading ? '…' : String(stats?.projects.total ?? 0)}
          sub={loading ? '' : `${stats?.projects.published ?? 0} publicados · ${stats?.projects.draft ?? 0} borradores`}
          color="indigo"
        />
        <StatCard
          label="Habilidades"
          value={loading ? '…' : String(stats?.skills ?? 0)}
          sub="Tecnologías registradas"
          color="violet"
        />
        <StatCard
          label="Experiencias"
          value={loading ? '…' : String(stats?.experiences.total ?? 0)}
          sub={loading ? '' : `${stats?.experiences.current ?? 0} actual`}
          color="sky"
        />
        <StatCard
          label="Categorías"
          value={loading ? '…' : String(stats?.categories ?? 0)}
          sub="Para clasificar proyectos"
          color="emerald"
        />
      </div>

      {/* Accesos directos */}
      <h2 className="mb-3 text-sm font-semibold uppercase tracking-wider text-gray-400">
        Accesos rápidos
      </h2>
      <div className="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {NAV_CARDS.map(({ href, label, desc }) => (
          <Link key={href} href={href}
            className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm transition-shadow hover:shadow-md">
            <p className="font-semibold text-gray-900">{label}</p>
            <p className="mt-1 text-sm text-gray-400">{desc}</p>
          </Link>
        ))}
      </div>
    </div>
  );
}

function StatCard({ label, value, sub, color }: {
  label: string; value: string; sub: string;
  color: 'indigo' | 'violet' | 'sky' | 'emerald';
}) {
  const colors = {
    indigo: 'bg-indigo-50 text-indigo-700',
    violet: 'bg-violet-50 text-violet-700',
    sky: 'bg-sky-50 text-sky-700',
    emerald: 'bg-emerald-50 text-emerald-700',
  };
  return (
    <div className="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
      <p className="text-xs font-semibold uppercase tracking-wider text-gray-400">{label}</p>
      <p className={`my-2 inline-block rounded-lg px-2 py-0.5 text-3xl font-bold ${colors[color]}`}>
        {value}
      </p>
      <p className="text-xs text-gray-400">{sub}</p>
    </div>
  );
}
