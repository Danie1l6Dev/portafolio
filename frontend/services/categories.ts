import { api } from './api';
import type {
  ApiResponse,
  Category,
  CategoryPayload,
  PaginatedResponse,
} from '@/types';

// ── API admin (las categorías no tienen endpoint público propio) ──

export interface AdminCategoriesParams {
  page?: number;
}

export async function adminGetCategories(
  params: AdminCategoriesParams = {},
): Promise<PaginatedResponse<Category>> {
  const qs = new URLSearchParams();
  if (params.page) qs.set('page', String(params.page));

  const query = qs.toString() ? `?${qs}` : '';
  return api.get<PaginatedResponse<Category>>(`/admin/categories${query}`);
}

export async function adminGetCategory(id: number): Promise<Category> {
  const res = await api.get<ApiResponse<Category>>(`/admin/categories/${id}`);
  return res.data;
}

export async function adminCreateCategory(
  payload: CategoryPayload,
): Promise<Category> {
  const res = await api.post<ApiResponse<Category>>('/admin/categories', payload);
  return res.data;
}

export async function adminUpdateCategory(
  id: number,
  payload: CategoryPayload,
): Promise<Category> {
  const res = await api.patch<ApiResponse<Category>>(
    `/admin/categories/${id}`,
    payload,
  );
  return res.data;
}

export async function adminDeleteCategory(id: number): Promise<void> {
  await api.delete(`/admin/categories/${id}`);
}
