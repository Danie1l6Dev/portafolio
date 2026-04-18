import { api } from './api';
import type { AuthUser, LoginCredentials, LoginResponse } from '@/types';

export const TOKEN_KEY = 'auth_token';

// Persistencia de token (modo API Bearer)
export function saveToken(token: string): void {
  localStorage.setItem(TOKEN_KEY, token);
}

export function clearToken(): void {
  localStorage.removeItem(TOKEN_KEY);
}

export function getStoredToken(): string | null {
  if (typeof window === 'undefined') return null;
  return localStorage.getItem(TOKEN_KEY);
}

// Llamadas a la API
export async function login(credentials: LoginCredentials): Promise<{
  user: AuthUser;
  token: string;
}> {
  const res = await api.post<LoginResponse>('/auth/login', credentials);

  return {
    user: res.data.user,
    token: res.data.token,
  };
}

export async function logout(): Promise<void> {
  await api.post('/auth/logout', {});
}

export async function getMe(): Promise<AuthUser> {
  const res = await api.get<{ data: AuthUser }>('/auth/me');
  return res.data;
}
