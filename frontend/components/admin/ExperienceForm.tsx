'use client';

import { FormEvent, useEffect, useRef, useState } from 'react';
import { Button } from '@/components/ui/Button';
import { ApiError } from '@/services/api';
import type { Experience, ExperiencePayload } from '@/types';

interface ExperienceFormProps {
  initial?: Experience;
  onSubmit: (payload: ExperiencePayload) => Promise<void>;
  onCancel: () => void;
}

type FormState = {
  company: string; position: string; location: string;
  description: string; company_url: string;
  started_at: string; finished_at: string;
  is_current: boolean; sort_order: number;
  company_logo?: File;
};

const EMPTY: FormState = {
  company: '', position: '', location: '', description: '',
  company_url: '', started_at: '', finished_at: '',
  is_current: false, sort_order: 0,
};

export function ExperienceForm({ initial, onSubmit, onCancel }: ExperienceFormProps) {
  const [form, setForm] = useState<FormState>(EMPTY);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [globalError, setGlobalError] = useState('');
  const [logoPreview, setLogoPreview] = useState<string | null>(null);
  const fileRef = useRef<HTMLInputElement>(null);

  useEffect(() => {
    if (initial) {
      setForm({
        company: initial.company, position: initial.position,
        location: initial.location ?? '', description: initial.description ?? '',
        company_url: initial.company_url ?? '',
        started_at: initial.started_at?.slice(0, 10) ?? '',
        finished_at: initial.finished_at?.slice(0, 10) ?? '',
        is_current: initial.is_current, sort_order: 0,
      });
      setLogoPreview(initial.company_logo ?? null);
    } else {
      setForm(EMPTY);
      setLogoPreview(null);
    }
  }, [initial]);

  function set(key: keyof FormState, value: unknown) {
    setForm((f) => ({ ...f, [key]: value }));
    setErrors((e) => ({ ...e, [key]: '' }));
  }

  function handleLogo(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    set('company_logo', file);
    setLogoPreview(URL.createObjectURL(file));
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setGlobalError('');
    try {
      const payload: ExperiencePayload = {
        company: form.company, position: form.position,
        location: form.location || undefined,
        description: form.description || undefined,
        company_url: form.company_url || undefined,
        started_at: form.started_at,
        finished_at: form.is_current ? null : (form.finished_at || null),
        is_current: form.is_current,
        sort_order: Number(form.sort_order),
        ...(form.company_logo ? { company_logo: form.company_logo } : {}),
      };
      await onSubmit(payload);
    } catch (err) {
      if (err instanceof ApiError && err.errors) {
        const mapped: Record<string, string> = {};
        for (const [k, msgs] of Object.entries(err.errors)) mapped[k] = msgs[0];
        setErrors(mapped);
      }
      setGlobalError(err instanceof Error ? err.message : 'Error al guardar');
    } finally {
      setLoading(false);
    }
  }

  return (
    <form onSubmit={handleSubmit} className="space-y-4">
      {globalError && <p className="rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">{globalError}</p>}

      <div className="grid grid-cols-2 gap-4">
        <Field label="Empresa *" error={errors.company}>
          <input type="text" required value={form.company} onChange={(e) => set('company', e.target.value)}
            className={inputCls(!!errors.company)} placeholder="Nombre de la empresa" />
        </Field>
        <Field label="Cargo *" error={errors.position}>
          <input type="text" required value={form.position} onChange={(e) => set('position', e.target.value)}
            className={inputCls(!!errors.position)} placeholder="Tu cargo o rol" />
        </Field>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <Field label="Ubicación" error={errors.location}>
          <input type="text" value={form.location} onChange={(e) => set('location', e.target.value)}
            className={inputCls(!!errors.location)} placeholder="Ciudad, País" />
        </Field>
        <Field label="URL empresa" error={errors.company_url}>
          <input type="url" value={form.company_url} onChange={(e) => set('company_url', e.target.value)}
            className={inputCls(!!errors.company_url)} placeholder="https://empresa.com" />
        </Field>
      </div>

      <Field label="Descripción" error={errors.description}>
        <textarea rows={3} value={form.description} onChange={(e) => set('description', e.target.value)}
          className={inputCls(!!errors.description)} placeholder="Describe brevemente el rol..." />
      </Field>

      <div className="grid grid-cols-2 gap-4">
        <Field label="Fecha inicio *" error={errors.started_at}>
          <input type="date" required value={form.started_at} onChange={(e) => set('started_at', e.target.value)}
            className={inputCls(!!errors.started_at)} />
        </Field>
        <Field label="Fecha fin" error={errors.finished_at}>
          <input type="date" value={form.finished_at} disabled={form.is_current}
            onChange={(e) => set('finished_at', e.target.value)}
            className={`${inputCls(!!errors.finished_at)} ${form.is_current ? 'cursor-not-allowed opacity-50' : ''}`} />
        </Field>
      </div>

      <label className="flex cursor-pointer items-center gap-2">
        <input type="checkbox" checked={form.is_current} onChange={(e) => set('is_current', e.target.checked)}
          className="h-4 w-4 rounded border-gray-300 text-sky-600" />
        <span className="text-sm text-gray-700">Posición actual (sin fecha de fin)</span>
      </label>

      {/* Logo */}
      <Field label="Logo de la empresa" error={errors.company_logo}>
        <div className="flex items-center gap-4">
          {logoPreview && (
            <img src={logoPreview} alt="Logo" className="h-12 w-12 rounded-lg border border-gray-200 object-contain p-1" />
          )}
          <input ref={fileRef} type="file" accept="image/*,.svg" onChange={handleLogo} className="hidden" />
          <Button type="button" variant="secondary" size="sm" onClick={() => fileRef.current?.click()}>
            {logoPreview ? 'Cambiar logo' : 'Subir logo'}
          </Button>
          {logoPreview && !initial?.company_logo && (
            <span className="text-xs text-gray-400">{form.company_logo?.name}</span>
          )}
        </div>
      </Field>

      <div className="flex justify-end gap-3 border-t border-gray-100 pt-4">
        <Button type="button" variant="secondary" onClick={onCancel}>Cancelar</Button>
        <Button type="submit" loading={loading}>{initial ? 'Actualizar' : 'Crear'} experiencia</Button>
      </div>
    </form>
  );
}

function inputCls(hasError: boolean) {
  return `w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 ${hasError ? 'border-red-400' : 'border-gray-300'}`;
}

function Field({ label, error, children }: { label: string; error?: string; children: React.ReactNode }) {
  return (
    <div>
      <label className="mb-1 block text-sm font-medium text-gray-700">{label}</label>
      {children}
      {error && <p className="mt-1 text-xs text-red-500">{error}</p>}
    </div>
  );
}
