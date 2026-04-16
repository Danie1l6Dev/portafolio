'use client';

import { FormEvent, useState } from 'react';
import { sendContactMessage, type ContactPayload } from '@/services/messages';
import { Button } from '@/components/ui/Button';
import { ApiError } from '@/services/api';

type FormState = 'idle' | 'loading' | 'success' | 'error';

const EMPTY: ContactPayload = { name: '', email: '', subject: '', body: '' };

export function ContactForm() {
  const [form, setForm] = useState<ContactPayload>(EMPTY);
  const [state, setState] = useState<FormState>('idle');
  const [serverMessage, setServerMessage] = useState('');
  const [fieldErrors, setFieldErrors] = useState<Record<string, string>>({});

  function set(key: keyof ContactPayload, value: string) {
    setForm((f) => ({ ...f, [key]: value }));
    setFieldErrors((e) => ({ ...e, [key]: '' }));
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setState('loading');
    setFieldErrors({});
    setServerMessage('');

    try {
      const res = await sendContactMessage(form);
      setState('success');
      setServerMessage(res.message);
      setForm(EMPTY);
    } catch (err) {
      setState('error');
      if (err instanceof ApiError && err.errors) {
        // Mapear los primeros errores de cada campo
        const mapped: Record<string, string> = {};
        for (const [key, msgs] of Object.entries(err.errors)) {
          mapped[key] = msgs[0];
        }
        setFieldErrors(mapped);
        setServerMessage('Revisa los campos e inténtalo de nuevo.');
      } else {
        setServerMessage(
          err instanceof Error
            ? err.message
            : 'Error al enviar el mensaje. Inténtalo más tarde.',
        );
      }
    }
  }

  if (state === 'success') {
    return (
      <div className="rounded-xl border border-green-200 bg-green-50 p-8 text-center">
        <div className="mb-3 text-3xl">✓</div>
        <h3 className="mb-1 text-lg font-semibold text-green-800">
          Mensaje enviado
        </h3>
        <p className="text-sm text-green-700">{serverMessage}</p>
        <button
          onClick={() => setState('idle')}
          className="mt-4 text-sm text-green-600 underline hover:text-green-800"
        >
          Enviar otro mensaje
        </button>
      </div>
    );
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-5" noValidate>
      {/* Nombre + Email */}
      <div className="grid gap-5 sm:grid-cols-2">
        <Field
          label="Nombre"
          id="name"
          type="text"
          value={form.name}
          onChange={(v) => set('name', v)}
          error={fieldErrors.name}
          placeholder="Tu nombre"
          required
        />
        <Field
          label="Correo electrónico"
          id="email"
          type="email"
          value={form.email}
          onChange={(v) => set('email', v)}
          error={fieldErrors.email}
          placeholder="tu@correo.com"
          required
        />
      </div>

      {/* Asunto */}
      <Field
        label="Asunto"
        id="subject"
        type="text"
        value={form.subject}
        onChange={(v) => set('subject', v)}
        error={fieldErrors.subject}
        placeholder="¿En qué puedo ayudarte?"
        required
      />

      {/* Mensaje */}
      <div>
        <label
          htmlFor="body"
          className="mb-1 block text-sm font-medium text-gray-700"
        >
          Mensaje <span className="text-red-500">*</span>
        </label>
        <textarea
          id="body"
          rows={6}
          required
          value={form.body}
          onChange={(e) => set('body', e.target.value)}
          placeholder="Escribe tu mensaje aquí..."
          className={`w-full resize-y rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
            fieldErrors.body
              ? 'border-red-400 focus:ring-red-400'
              : 'border-gray-300'
          }`}
        />
        {fieldErrors.body && (
          <p className="mt-1 text-xs text-red-500">{fieldErrors.body}</p>
        )}
        <p className="mt-1 text-right text-xs text-gray-400">
          {form.body.length} / 3000
        </p>
      </div>

      {/* Error global */}
      {state === 'error' && serverMessage && !Object.keys(fieldErrors).length && (
        <p className="rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">
          {serverMessage}
        </p>
      )}

      <Button
        type="submit"
        loading={state === 'loading'}
        className="w-full sm:w-auto"
      >
        Enviar mensaje
      </Button>
    </form>
  );
}

// ── Campo reutilizable ────────────────────────────────────────

interface FieldProps {
  label: string;
  id: string;
  type: string;
  value: string;
  onChange: (v: string) => void;
  error?: string;
  placeholder?: string;
  required?: boolean;
}

function Field({ label, id, type, value, onChange, error, placeholder, required }: FieldProps) {
  return (
    <div>
      <label htmlFor={id} className="mb-1 block text-sm font-medium text-gray-700">
        {label} {required && <span className="text-red-500">*</span>}
      </label>
      <input
        id={id}
        type={type}
        value={value}
        required={required}
        placeholder={placeholder}
        onChange={(e) => onChange(e.target.value)}
        className={`w-full rounded-lg border px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 ${
          error ? 'border-red-400 focus:ring-red-400' : 'border-gray-300'
        }`}
      />
      {error && <p className="mt-1 text-xs text-red-500">{error}</p>}
    </div>
  );
}
