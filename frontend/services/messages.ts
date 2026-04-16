import { api } from './api';

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

export async function sendContactMessage(
  payload: ContactPayload,
): Promise<ContactResponse> {
  return api.post<ContactResponse>('/contact', payload);
}
