import { api } from './api';
import type {
  ApiResponse,
  Skill,
  SkillPayload,
  SkillsResponse,
} from '@/types';

// ── API pública ───────────────────────────────────────────────

export async function getSkills(): Promise<SkillsResponse> {
  return api.get<SkillsResponse>('/skills');
}

// ── API admin ─────────────────────────────────────────────────

export interface AdminSkillsParams {
  page?: number;
  group?: string;
}

export async function adminGetSkills(
  params: AdminSkillsParams = {},
): Promise<Skill[]> {
  const qs = new URLSearchParams();
  if (params.page)  qs.set('page', String(params.page));
  if (params.group) qs.set('group', params.group);

  const query = qs.toString() ? `?${qs}` : '';
  const res = await api.get<{ data: Skill[] }>(`/admin/skills${query}`);
  return res.data;
}

export async function adminGetSkill(id: number): Promise<Skill> {
  const res = await api.get<ApiResponse<Skill>>(`/admin/skills/${id}`);
  return res.data;
}

export async function adminCreateSkill(payload: SkillPayload): Promise<Skill> {
  const res = await api.post<ApiResponse<Skill>>('/admin/skills', payload);
  return res.data;
}

export async function adminUpdateSkill(
  id: number,
  payload: SkillPayload,
): Promise<Skill> {
  const res = await api.patch<ApiResponse<Skill>>(`/admin/skills/${id}`, payload);
  return res.data;
}

export async function adminDeleteSkill(id: number): Promise<void> {
  await api.delete(`/admin/skills/${id}`);
}
