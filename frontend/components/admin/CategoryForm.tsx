'use client';

import { FormEvent, useEffect, useState } from 'react';
import { Button } from '@/components/ui/Button';
import { ApiError } from '@/services/api';
import type { Category, CategoryPayload } from '@/types';

interface CategoryFormProps {
  initial?: Category;
  onSubmit: (payload: CategoryPayload) => Promise<void>;
  onCancel: () => void;
}

const EMPTY: CategoryPayload = { name: '', description: '', color: '', sort_order: 0 };

export function CategoryForm({ initial, onSubmit, onCancel }: CategoryFormProps) {
  const [form, setForm] = useState<CategoryPayload>(EMPTY);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [globalError, setGlobalError] = useState('');

  useEffect(() => {
    if (initial) {
      setForm({
        name: initial.name,
        description: initial.description ?? '',
        color: initial.color ?? '',
        sort_order: initial.sort_order,
      });
    } else {
      setForm(EMPTY);
    }
  }, [initial]);

  function set(key: keyof CategoryPayload, value: string | number) {
    setForm((f) => ({ ...f, [key]: value }));
    setErrors((e) => ({ ...e, [key]: '' }));
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setGlobalError('');
    try {
      const payload: CategoryPayload = {
        name: form.name,
        ...(form.description ? { description: form.description } : {}),
        ...(form.color ? { color: form.color } : {}),
        sort_order: Number(form.sort_order ?? 0),
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
      {globalError && (
        <p className="rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">{globalError}</p>
      )}

      <Field label="Nombre *" error={errors.name}>
        <input
          type="text" required value={form.name as string}
          onChange={(e) => set('name', e.target.value)}
          className={inputCls(!!errors.name)}
          placeholder="Ej: Frontend"
        />
      </Field>

      <Field label="Descripción" error={errors.description}>
        <textarea
          rows={3} value={form.description as string}
          onChange={(e) => set('description', e.target.value)}
          className={inputCls(!!errors.description)}
          placeholder="Descripción opcional"
        />
      </Field>

      <div className="grid grid-cols-2 gap-4">
        <Field label="Color (hex)" error={errors.color}>
          <div className="flex gap-2">
            <input
              type="color"
              value={form.color || '#6366f1'}
              onChange={(e) => set('color', e.target.value)}
              className="h-9 w-12 cursor-pointer rounded-md border border-gray-300 p-0.5"
            />
            <input
              type="text" value={form.color as string}
              onChange={(e) => set('color', e.target.value)}
              className={`flex-1 ${inputCls(!!errors.color)}`}
              placeholder="#6366f1"
              maxLength={7}
            />
          </div>
        </Field>

        <Field label="Orden" error={errors.sort_order}>
          <input
            type="number" min={0} value={form.sort_order as number}
            onChange={(e) => set('sort_order', parseInt(e.target.value) || 0)}
            className={inputCls(!!errors.sort_order)}
          />
        </Field>
      </div>

      <div className="flex justify-end gap-3 border-t border-gray-100 pt-4">
        <Button type="button" variant="secondary" onClick={onCancel}>Cancelar</Button>
        <Button type="submit" loading={loading}>
          {initial ? 'Actualizar' : 'Crear'} categoría
        </Button>
      </div>
    </form>
  );
}

function inputCls(hasError: boolean) {
  return `w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500 ${
    hasError ? 'border-red-400' : 'border-gray-300'
  }`;
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
