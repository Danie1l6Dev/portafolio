import { api } from './api';
import type {
  ApiResponse,
  Experience,
  ExperiencePayload,
  PaginatedResponse,
} from '@/types';

// ── API pública ───────────────────────────────────────────────

export async function getExperiences(): Promise<Experience[]> {
  const res = await api.get<{ data: Experience[] }>('/experiences');
  return res.data;
}

// ── API admin ─────────────────────────────────────────────────

export interface AdminExperiencesParams {
  page?: number;
}

export async function adminGetExperiences(
  params: AdminExperiencesParams = {},
): Promise<PaginatedResponse<Experience>> {
  const qs = new URLSearchParams();
  if (params.page) qs.set('page', String(params.page));

  const query = qs.toString() ? `?${qs}` : '';
  return api.get<PaginatedResponse<Experience>>(`/admin/experiences${query}`);
}

export async function adminGetExperience(id: number): Promise<Experience> {
  const res = await api.get<ApiResponse<Experience>>(`/admin/experiences/${id}`);
  return res.data;
}

export async function adminCreateExperience(
  payload: ExperiencePayload,
): Promise<Experience> {
  const form = toFormData(payload);
  const res = await api.upload<ApiResponse<Experience>>('/admin/experiences', form);
  return res.data;
}

export async function adminUpdateExperience(
  id: number,
  payload: ExperiencePayload,
): Promise<Experience> {
  const form = toFormData(payload);
  const res = await api.upload<ApiResponse<Experience>>(
    `/admin/experiences/${id}`,
    form,
    'PATCH',
  );
  return res.data;
}

export async function adminDeleteExperience(id: number): Promise<void> {
  await api.delete(`/admin/experiences/${id}`);
}

// ── Helper FormData ───────────────────────────────────────────

function toFormData(payload: ExperiencePayload): FormData {
  const form = new FormData();

  (Object.entries(payload) as [string, unknown][]).forEach(([key, val]) => {
    if (val === undefined) return;

    if (val instanceof File) {
      form.append(key, val);
    } else if (val === null) {
      form.append(key, '');
    } else if (typeof val === 'boolean') {
      form.append(key, val ? '1' : '0');
    } else {
      form.append(key, String(val));
    }
  });

  return form;
}
