import { api } from './api';

// ── Tipos ─────────────────────────────────────────────────────

export interface ContactPayload {
  name: string;
  email: string;
  subject: string;
  body: string;
}

export interface ContactResponse {
  message: string;
  data: { id: number };
}

export interface Message {
  id: number;
  name: string;
  email: string;
  subject: string | null;
  body: string;
  is_read: boolean;
  read_at: string | null;
  ip_address: string | null;
  created_at: string;
}

export interface MessagesResponse {
  data: Message[];
  meta: {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    unread_count: number;
  };
}

export type MessageFilter = 'all' | 'unread';

// ── API pública ───────────────────────────────────────────────

export async function sendContactMessage(
  payload: ContactPayload,
): Promise<ContactResponse> {
  return api.post<ContactResponse>('/contact', payload);
}

// ── API admin ─────────────────────────────────────────────────

export async function adminGetMessages(params: {
  page?: number;
  filter?: MessageFilter;
} = {}): Promise<MessagesResponse> {
  const qs = new URLSearchParams();
  if (params.page && params.page > 1) qs.set('page', String(params.page));
  if (params.filter === 'unread')     qs.set('filter', 'unread');
  const query = qs.toString() ? `?${qs}` : '';
  return api.get<MessagesResponse>(`/admin/messages${query}`);
}

export async function adminGetMessage(id: number): Promise<Message> {
  const res = await api.get<{ data: Message }>(`/admin/messages/${id}`);
  return res.data;
}

export async function adminMarkMessageRead(id: number): Promise<Message> {
  const res = await api.patch<{ data: Message }>(`/admin/messages/${id}/read`, {});
  return res.data;
}

export async function adminMarkAllMessagesRead(): Promise<{ count: number; message: string }> {
  return api.post<{ count: number; message: string }>('/admin/messages/mark-all-read', {});
}

export async function adminDeleteMessage(id: number): Promise<void> {
  await api.delete(`/admin/messages/${id}`);
}
