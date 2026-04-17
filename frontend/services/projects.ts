import { api } from './api';
import type {
  ApiResponse,
  PaginatedResponse,
  Project,
  ProjectPayload,
} from '@/types';

// ── API pública ───────────────────────────────────────────────

export interface ProjectsParams {
  page?: number;
  category?: string;
  featured?: boolean;
}

export async function getProjects(
  params: ProjectsParams = {},
): Promise<PaginatedResponse<Project>> {
  const qs = new URLSearchParams();
  if (params.page)     qs.set('page', String(params.page));
  if (params.category) qs.set('category', params.category);
  if (params.featured) qs.set('featured', '1');

  const query = qs.toString() ? `?${qs}` : '';
  return api.get<PaginatedResponse<Project>>(`/projects${query}`);
}

export async function getProject(id: number): Promise<Project> {
  const res = await api.get<ApiResponse<Project>>(`/projects/${id}`);
  return res.data;
}

// ── API admin ─────────────────────────────────────────────────

export interface AdminProjectsParams {
  page?: number;
  status?: string;
  category?: string;
}

export async function adminGetProjects(
  params: AdminProjectsParams = {},
): Promise<PaginatedResponse<Project>> {
  const qs = new URLSearchParams();
  if (params.page)     qs.set('page', String(params.page));
  if (params.status)   qs.set('status', params.status);
  if (params.category) qs.set('category', params.category);

  const query = qs.toString() ? `?${qs}` : '';
  return api.get<PaginatedResponse<Project>>(`/admin/projects${query}`);
}

export async function adminGetProject(id: number): Promise<Project> {
  const res = await api.get<ApiResponse<Project>>(`/admin/projects/${id}`);
  return res.data;
}

export async function adminCreateProject(
  payload: ProjectPayload,
): Promise<Project> {
  const form = toFormData(payload);
  const res = await api.upload<ApiResponse<Project>>('/admin/projects', form);
  return res.data;
}

export async function adminUpdateProject(
  id: number,
  payload: ProjectPayload,
): Promise<Project> {
  const form = toFormData(payload);
  const res = await api.upload<ApiResponse<Project>>(
    `/admin/projects/${id}`,
    form,
    'PATCH',
  );
  return res.data;
}

export async function adminDeleteProject(id: number): Promise<void> {
  await api.delete(`/admin/projects/${id}`);
}

// ── Helper FormData ───────────────────────────────────────────

function toFormData(payload: ProjectPayload): FormData {
  const form = new FormData();

  (Object.entries(payload) as [string, unknown][]).forEach(([key, val]) => {
    if (val === undefined) return;

    if (key === 'skill_ids' && Array.isArray(val)) {
      val.forEach((id) => form.append('skill_ids[]', String(id)));
    } else if (val instanceof File) {
      form.append(key, val);
    } else if (val === null) {
      form.append(key, '');
    } else {
      form.append(key, String(val));
    }
  });

  return form;
}
