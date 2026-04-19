'use client';

import { FormEvent, useEffect, useState } from 'react';
import { useRouter, useSearchParams } from 'next/navigation';
import { useAuth } from '@/hooks/useAuth';
import { Button } from '@/components/ui/Button';

export default function LoginClient() {
  const router = useRouter();
  const searchParams = useSearchParams();
  const { user, login, loading, error } = useAuth({ checkSession: false });

  const [email, setEmail] = useState('');
  const [password, setPassword] = useState('');

  useEffect(() => {
    if (user) {
      const next = searchParams.get('next');
      router.replace(next?.startsWith('/admin') ? next : '/admin');
    }
  }, [user, searchParams, router]);

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    await login(
      { email, password },
      { redirectTo: searchParams.get('next') ?? undefined },
    );
  }

  return (
    <div className="w-full max-w-sm animate-slide-up">
      <div className="rounded-2xl border border-slate-200 bg-white p-8 shadow-[0_4px_24px_-4px_rgb(0_0_0_/_0.1)]">
        <div className="mb-6 flex flex-col items-center text-center">
          <span className="mb-3 flex h-10 w-10 items-center justify-center rounded-xl bg-sky-600 text-sm font-black text-white shadow-sm">
            DS
          </span>
          <h1 className="text-lg font-bold text-slate-900">Panel Admin</h1>
          <p className="mt-0.5 text-sm text-slate-400">Inicia sesion para continuar</p>
        </div>

        {error && (
          <div className="mb-5 rounded-lg border border-red-100 bg-red-50 px-4 py-3 text-sm text-red-600">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-4">
          <div>
            <label htmlFor="email" className="mb-1.5 block text-sm font-medium text-slate-700">
              Correo electronico
            </label>
            <input
              id="email"
              type="email"
              required
              autoComplete="email"
              value={email}
              onChange={(e) => setEmail(e.target.value)}
              className="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 placeholder:text-slate-400 transition-colors hover:border-slate-300 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1"
              placeholder="admin@ejemplo.com"
            />
          </div>

          <div>
            <label htmlFor="password" className="mb-1.5 block text-sm font-medium text-slate-700">
              Contrasena
            </label>
            <input
              id="password"
              type="password"
              required
              autoComplete="current-password"
              value={password}
              onChange={(e) => setPassword(e.target.value)}
              className="w-full rounded-lg border border-slate-200 bg-white px-3.5 py-2.5 text-sm text-slate-900 transition-colors hover:border-slate-300 focus:border-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:ring-offset-1"
              placeholder="********"
            />
          </div>

          <Button type="submit" loading={loading} size="lg" className="w-full">
            Ingresar
          </Button>
        </form>
      </div>

      <p className="mt-4 text-center text-xs text-slate-400">
        Acceso restringido solo a administradores.
      </p>
    </div>
  );
}
