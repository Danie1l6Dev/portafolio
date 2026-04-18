// ─────────────────────────────────────────────────────────────
// Cliente base de la API Laravel
// Todos los servicios importan desde aquí.
// ─────────────────────────────────────────────────────────────

import { API } from '@/lib/constants';

const API_BASE =
  (process.env.NEXT_PUBLIC_API_URL ??
    'http://localhost:8000') + '/api/v1';
// ── Token ─────────────────────────────────────────────────────

/** Lee el token Bearer desde localStorage (solo cliente). */
function getToken(): string | null {
  if (typeof window === 'undefined') return null;
  return localStorage.getItem('auth_token');
}

// ── Headers ───────────────────────────────────────────────────

function buildHeaders(extra: HeadersInit = {}): HeadersInit {
  const token = getToken();
  return {
    Accept: 'application/json',
    'Content-Type': 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
    ...extra,
  };
}

// ── Errores normalizados ──────────────────────────────────────

/**
 * Error de la API (respuesta HTTP con status >= 400).
 * Incluye los errores de validación de Laravel si los hay.
 */
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

/**
 * Error de red o timeout (no se recibió respuesta del servidor).
 * Útil para mostrar mensajes distintos en la UI ("Sin conexión" vs "Error del servidor").
 */
export class NetworkError extends Error {
  constructor(message = 'No se pudo conectar con el servidor. Verifica tu conexión.') {
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

// ── Fetch con timeout ─────────────────────────────────────────

/**
 * Envuelve fetch con un AbortController que cancela la petición
 * si supera API.timeoutMs (por defecto 10 segundos).
 *
 * Lanza NetworkError si la petición se cancela por timeout o error de red.
 */
async function fetchWithTimeout(
  url: string,
  options: RequestInit,
  timeoutMs = API.timeoutMs,
): Promise<Response> {
  const controller = new AbortController();
  const timerId    = setTimeout(() => controller.abort(), timeoutMs);

  try {
    const res = await fetch(url, { ...options, signal: controller.signal });
    return res;
  } catch (err) {
    if (err instanceof DOMException && err.name === 'AbortError') {
      throw new NetworkError(`La petición tardó más de ${timeoutMs / 1000}s y fue cancelada.`);
    }
    // TypeError: failed to fetch (sin conexión, CORS, etc.)
    throw new NetworkError();
  } finally {
    clearTimeout(timerId);
  }
}

// ── Peticiones JSON ───────────────────────────────────────────

export async function apiFetch<T>(
  endpoint: string,
  options: RequestInit = {},
): Promise<T> {
  const res = await fetchWithTimeout(`${API_BASE}${endpoint}`, {
    ...options,
    headers: buildHeaders(options.headers),
  });
  return handleResponse<T>(res);
}

// ── Petición multipart/form-data (subida de archivos) ─────────
// No se fija Content-Type — el navegador lo pone con el boundary correcto.

export async function apiUpload<T>(
  endpoint: string,
  formData: FormData,
  method: 'POST' | 'PUT' | 'PATCH' = 'POST',
): Promise<T> {
  const token = getToken();
  const headers: HeadersInit = {
    Accept: 'application/json',
    ...(token ? { Authorization: `Bearer ${token}` } : {}),
  };

  // Laravel no acepta PUT/PATCH con FormData en todos los entornos.
  // Se usa POST + _method spoofing.
  if (method !== 'POST') {
    formData.append('_method', method);
  }

  const res = await fetchWithTimeout(`${API_BASE}${endpoint}`, {
    method: 'POST',
    headers,
    body: formData,
  });
  return handleResponse<T>(res);
}

// ── Atajos tipados ────────────────────────────────────────────

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
