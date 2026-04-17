'use client';

import { useState, useEffect, useCallback } from 'react';
import { useRouter } from 'next/navigation';
import {
  login as apiLogin,
  logout as apiLogout,
  getMe,
  saveToken,
  clearToken,
  getStoredToken,
} from '@/services/auth';
import type { AuthUser, LoginCredentials } from '@/types';

interface AuthState {
  user: AuthUser | null;
  loading: boolean;
  error: string | null;
}

export function useAuth() {
  const router = useRouter();
  const [state, setState] = useState<AuthState>({
    user: null,
    loading: true,
    error: null,
  });

  // Carga el usuario al montar si hay token almacenado
  useEffect(() => {
    const token = getStoredToken();
    if (!token) {
      setState({ user: null, loading: false, error: null });
      return;
    }

    getMe()
      .then((user) => setState({ user, loading: false, error: null }))
      .catch(() => {
        clearToken();
        setState({ user: null, loading: false, error: null });
      });
  }, []);

  const login = useCallback(
    async (credentials: LoginCredentials) => {
      setState((s) => ({ ...s, loading: true, error: null }));
      try {
        const { user, token } = await apiLogin(credentials);
        saveToken(token);
        setState({ user, loading: false, error: null });
        router.push('/admin');
      } catch (err) {
        const message =
          err instanceof Error ? err.message : 'Error al iniciar sesión';
        setState((s) => ({ ...s, loading: false, error: message }));
      }
    },
    [router],
  );

  const logout = useCallback(async () => {
    setState((s) => ({ ...s, loading: true }));
    try {
      await apiLogout();
    } finally {
      clearToken();
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
