'use client';

import { useState, useEffect, useCallback } from 'react';
import { usePathname, useRouter } from 'next/navigation';
import { login as apiLogin, logout as apiLogout, getMe } from '@/services/auth';
import type { AuthUser, LoginCredentials } from '@/types';

interface AuthState {
  user: AuthUser | null;
  loading: boolean;
  error: string | null;
}

interface LoginOptions {
  redirectTo?: string;
}

interface UseAuthOptions {
  checkSession?: boolean;
}

export function useAuth(options: UseAuthOptions = {}) {
  const { checkSession = true } = options;
  const router = useRouter();
  const pathname = usePathname();
  const shouldCheckSession = checkSession && pathname !== '/login';
  const [state, setState] = useState<AuthState>({
    user: null,
    loading: shouldCheckSession,
    error: null,
  });

  useEffect(() => {
    if (!shouldCheckSession) return;

    getMe()
      .then((user) => setState({ user, loading: false, error: null }))
      .catch(() => {
        setState({ user: null, loading: false, error: null });
      });
  }, [shouldCheckSession]);

  const login = useCallback(
    async (credentials: LoginCredentials, options?: LoginOptions) => {
      setState((prev) => ({ ...prev, loading: true, error: null }));

      try {
        const user = await apiLogin(credentials);
        setState({ user, loading: false, error: null });

        const redirectTo = options?.redirectTo?.startsWith('/admin')
          ? options.redirectTo
          : '/admin';

        router.push(redirectTo);
      } catch (err) {
        const message =
          err instanceof Error ? err.message : 'Error al iniciar sesion';

        setState((prev) => ({ ...prev, loading: false, error: message }));
      }
    },
    [router],
  );

  const logout = useCallback(async () => {
    setState((prev) => ({ ...prev, loading: true }));

    try {
      await apiLogout();
    } finally {
      setState({ user: null, loading: false, error: null });
      router.push('/login');
    }
  }, [router]);

  return {
    user: state.user,
    loading: state.loading,
    error: state.error,
    isAuthenticated: !!state.user,
    login,
    logout,
  };
}
