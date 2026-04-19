import { API } from '@/lib/constants';

const rawApiUrl = process.env.NEXT_PUBLIC_API_URL ?? 'http://localhost:8000';
const normalizedApiUrl = rawApiUrl.replace(/\/+$/, '');

export const BACKEND_ORIGIN = normalizedApiUrl.endsWith('/api')
  ? normalizedApiUrl.slice(0, -4)
  : normalizedApiUrl;

export const API_ROOT = `${BACKEND_ORIGIN}/api`;
const API_BASE = `${API_ROOT}/v1`;

const MUTATION_METHODS = new Set(['POST', 'PUT', 'PATCH', 'DELETE']);
let csrfTokenCache: string | null = null;

function getXsrfTokenFromCookie(): string | null {
  if (typeof document === 'undefined') return null;

  const tokenCookie = document.cookie
    .split('; ')
    .find((cookie) => cookie.startsWith('XSRF-TOKEN='));

  if (!tokenCookie) return null;

  return decodeURIComponent(tokenCookie.split('=').slice(1).join('='));
}

function mergeHeaders(base: Record<string, string>, extra: HeadersInit = {}): Headers {
  const headers = new Headers(base);
  const extraHeaders = new Headers(extra);

  extraHeaders.forEach((value, key) => {
    headers.set(key, value);
  });

  return headers;
}

function buildHeaders(method: string, extra: HeadersInit = {}): Headers {
  const upperMethod = method.toUpperCase();
  const baseHeaders: Record<string, string> = {
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  };

  if (!['GET', 'HEAD'].includes(upperMethod)) {
    baseHeaders['Content-Type'] = 'application/json';
  }

  if (MUTATION_METHODS.has(upperMethod)) {
    const xsrfCookieToken = getXsrfTokenFromCookie();

    if (xsrfCookieToken) {
      baseHeaders['X-XSRF-TOKEN'] = xsrfCookieToken;
    } else if (csrfTokenCache) {
      baseHeaders['X-CSRF-TOKEN'] = csrfTokenCache;
    }
  }

  return mergeHeaders(baseHeaders, extra);
}

function hasCsrfHeader(headers: Headers): boolean {
  return headers.has('X-CSRF-TOKEN') || headers.has('X-XSRF-TOKEN');
}

export class ApiError extends Error {
  constructor(
    public readonly status: number,
    message: string,
    public readonly errors?: Record<string, string[]>,
  ) {
    super(message);
    this.name = 'ApiError';
  }
}

export class NetworkError extends Error {
  constructor(message = 'No se pudo conectar con el servidor. Verifica tu conexion.') {
    super(message);
    this.name = 'NetworkError';
  }
}

async function handleResponse<T>(res: Response): Promise<T> {
  if (res.ok) {
    if (res.status === 204) return undefined as T;
    return res.json() as Promise<T>;
  }

  let body: { message?: string; errors?: Record<string, string[]> } = {};
  try {
    body = await res.json();
  } catch {
    // respuesta sin cuerpo JSON
  }

  throw new ApiError(
    res.status,
    body.message ?? `HTTP ${res.status}`,
    body.errors,
  );
}

async function fetchWithTimeout(
  url: string,
  options: RequestInit,
  timeoutMs = API.timeoutMs,
): Promise<Response> {
  const controller = new AbortController();
  const timerId = setTimeout(() => controller.abort(), timeoutMs);

  try {
    const res = await fetch(url, {
      ...options,
      credentials: options.credentials ?? 'include',
      signal: controller.signal,
    });

    return res;
  } catch (err) {
    if (err instanceof DOMException && err.name === 'AbortError') {
      throw new NetworkError(`La peticion tardo mas de ${timeoutMs / 1000}s y fue cancelada.`);
    }

    throw new NetworkError();
  } finally {
    clearTimeout(timerId);
  }
}

async function requestJson<T>(
  url: string,
  options: RequestInit = {},
): Promise<T> {
  const method = (options.method ?? 'GET').toUpperCase();
  let headers = buildHeaders(method, options.headers);

  // In cross-domain deployments the token may not be readable from document.cookie.
  // Ensure we always bootstrap CSRF before any state-changing request.
  if (MUTATION_METHODS.has(method) && !hasCsrfHeader(headers)) {
    await ensureCsrfCookie();
    headers = buildHeaders(method, options.headers);
  }

  const res = await fetchWithTimeout(url, {
    ...options,
    method,
    headers,
  });

  return handleResponse<T>(res);
}

export async function ensureCsrfCookie(): Promise<void> {
  const res = await fetchWithTimeout(`${BACKEND_ORIGIN}/sanctum/csrf-cookie`, {
    method: 'GET',
    headers: mergeHeaders({
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    }),
  });

  if (!res.ok && res.status !== 204) {
    await handleResponse(res);
  }

  const tokenResponse = await fetchWithTimeout(`${API_ROOT}/csrf-token`, {
    method: 'GET',
    headers: mergeHeaders({
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    }),
  });

  if (!tokenResponse.ok) {
    await handleResponse(tokenResponse);
  }

  const body = (await tokenResponse.json()) as { csrf_token?: string };
  csrfTokenCache = body.csrf_token ?? null;
}

export async function backendFetch<T>(
  endpoint: string,
  options: RequestInit = {},
): Promise<T> {
  return requestJson<T>(`${BACKEND_ORIGIN}${endpoint}`, options);
}

export async function apiFetch<T>(
  endpoint: string,
  options: RequestInit = {},
): Promise<T> {
  return requestJson<T>(`${API_BASE}${endpoint}`, options);
}

export async function apiUpload<T>(
  endpoint: string,
  formData: FormData,
  method: 'POST' | 'PUT' | 'PATCH' = 'POST',
): Promise<T> {
  const upperMethod = method.toUpperCase();

  if (upperMethod !== 'POST') {
    formData.append('_method', upperMethod);
  }

  let headers = mergeHeaders({
    Accept: 'application/json',
    'X-Requested-With': 'XMLHttpRequest',
  });

  const xsrfCookieToken = getXsrfTokenFromCookie();
  if (xsrfCookieToken) {
    headers.set('X-XSRF-TOKEN', xsrfCookieToken);
  } else if (csrfTokenCache) {
    headers.set('X-CSRF-TOKEN', csrfTokenCache);
  }

  if (!hasCsrfHeader(headers)) {
    await ensureCsrfCookie();

    headers = mergeHeaders({
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    });

    const refreshedXsrfCookieToken = getXsrfTokenFromCookie();
    if (refreshedXsrfCookieToken) {
      headers.set('X-XSRF-TOKEN', refreshedXsrfCookieToken);
    } else if (csrfTokenCache) {
      headers.set('X-CSRF-TOKEN', csrfTokenCache);
    }
  }

  const res = await fetchWithTimeout(`${API_BASE}${endpoint}`, {
    method: 'POST',
    headers,
    body: formData,
  });

  return handleResponse<T>(res);
}

export const api = {
  get: <T>(url: string, init?: RequestInit) =>
    apiFetch<T>(url, { method: 'GET', ...init }),

  post: <T>(url: string, body: unknown) =>
    apiFetch<T>(url, { method: 'POST', body: JSON.stringify(body) }),

  patch: <T>(url: string, body: unknown) =>
    apiFetch<T>(url, { method: 'PATCH', body: JSON.stringify(body) }),

  delete: <T>(url: string) =>
    apiFetch<T>(url, { method: 'DELETE' }),

  upload: <T>(url: string, data: FormData, method?: 'POST' | 'PUT' | 'PATCH') =>
    apiUpload<T>(url, data, method),
};

export const backend = {
  get: <T>(url: string, init?: RequestInit) =>
    backendFetch<T>(url, { method: 'GET', ...init }),

  post: <T>(url: string, body: unknown) =>
    backendFetch<T>(url, { method: 'POST', body: JSON.stringify(body) }),
};
