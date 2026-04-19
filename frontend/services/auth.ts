import { backend, ensureCsrfCookie } from './api';
import type { AuthUser, LoginCredentials } from '@/types';

interface LoginResponse {
  data: {
    user: AuthUser;
  };
}

export async function login(credentials: LoginCredentials): Promise<AuthUser> {
  await ensureCsrfCookie();

  const res = await backend.post<LoginResponse>('/login', credentials);
  return res.data.user;
}

export async function logout(): Promise<void> {
  await ensureCsrfCookie();
  await backend.post('/logout', {});
}

export async function getMe(): Promise<AuthUser> {
  const res = await backend.get<{ data: AuthUser }>('/api/user');
  return res.data;
}
