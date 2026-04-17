import { api } from './api';
import type { AuthUser, LoginCredentials, LoginResponse } from '@/types';

// Nombre de la cookie que lee el middleware de Next.js
export const TOKEN_COOKIE = 'auth_token';
export const TOKEN_KEY    = 'auth_token';

// ── Helpers de persistencia ───────────────────────────────────

export function saveToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token);
  // Cookie sin httpOnly para que el middleware Edge pueda leerla
  document.cookie = `${TOKEN_COOKIE}=${token}; path=/; SameSite=Lax`;
}

export function clearToken(): void {
  localStorage.removeItem(TOKEN_KEY);
  document.cookie = `${TOKEN_COOKIE}=; path=/; max-age=0`;
}

export function getStoredToken(): string | null {
  if (typeof window === 'undefined') return null;
  return localStorage.getItem(TOKEN_KEY);
}

// ── Llamadas a la API ─────────────────────────────────────────

export async function login(credentials: LoginCredentials): Promise<{
  user: AuthUser;
  token: string;
}> {
  const res = await api.post<LoginResponse>('/auth/login', credentials);
  return res.data;
}

export async function logout(): Promise<void> {
  await api.post('/auth/logout', {});
}

export async function getMe(): Promise<AuthUser> {
  const res = await api.get<{ data: AuthUser }>('/auth/me');
  return res.data;
}
