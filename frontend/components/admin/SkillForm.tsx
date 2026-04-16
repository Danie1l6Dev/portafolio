'use client';

import { FormEvent, useEffect, useState } from 'react';
import { Button } from '@/components/ui/Button';
import { ApiError } from '@/services/api';
import { skillLevelLabel } from '@/lib/utils';
import type { Skill, SkillPayload } from '@/types';

interface SkillFormProps {
  initial?: Skill;
  onSubmit: (payload: SkillPayload) => Promise<void>;
  onCancel: () => void;
}

const EMPTY: SkillPayload = { name: '', group: '', icon: '', level: 3, sort_order: 0, is_featured: false };

export function SkillForm({ initial, onSubmit, onCancel }: SkillFormProps) {
  const [form, setForm] = useState<SkillPayload>(EMPTY);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [globalError, setGlobalError] = useState('');

  useEffect(() => {
    setForm(initial
      ? { name: initial.name, group: initial.group ?? '', icon: initial.icon ?? '', level: initial.level, sort_order: initial.sort_order, is_featured: initial.is_featured }
      : EMPTY);
  }, [initial]);

  function set(key: keyof SkillPayload, value: unknown) {
    setForm((f) => ({ ...f, [key]: value }));
    setErrors((e) => ({ ...e, [key]: '' }));
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setGlobalError('');
    try {
      const payload: SkillPayload = {
        name: form.name,
        level: Number(form.level),
        sort_order: Number(form.sort_order ?? 0),
        is_featured: Boolean(form.is_featured),
        ...(form.group ? { group: form.group } : {}),
        ...(form.icon ? { icon: form.icon } : {}),
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

      <div className="grid grid-cols-2 gap-4">
        <Field label="Nombre *" error={errors.name}>
          <input type="text" required value={form.name as string}
            onChange={(e) => set('name', e.target.value)}
            className={inputCls(!!errors.name)} placeholder="Ej: TypeScript" />
        </Field>

        <Field label="Grupo" error={errors.group}>
          <input type="text" value={form.group as string}
            onChange={(e) => set('group', e.target.value)}
            className={inputCls(!!errors.group)} placeholder="Ej: Frontend" />
        </Field>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <Field label="Icono (emoji o texto)" error={errors.icon}>
          <input type="text" value={form.icon as string}
            onChange={(e) => set('icon', e.target.value)}
            className={inputCls(!!errors.icon)} placeholder="Ej: ⚛️" maxLength={10} />
        </Field>

        <Field label="Nivel (1–5)" error={errors.level}>
          <select value={form.level as number}
            onChange={(e) => set('level', parseInt(e.target.value))}
            className={inputCls(!!errors.level)}>
            {[1, 2, 3, 4, 5].map((n) => (
              <option key={n} value={n}>{n} — {skillLevelLabel(n)}</option>
            ))}
          </select>
        </Field>
      </div>

      <div className="grid grid-cols-2 gap-4">
        <Field label="Orden" error={errors.sort_order}>
          <input type="number" min={0} value={form.sort_order as number}
            onChange={(e) => set('sort_order', parseInt(e.target.value) || 0)}
            className={inputCls(!!errors.sort_order)} />
        </Field>

        <Field label="Destacada" error={errors.is_featured}>
          <label className="mt-2 flex cursor-pointer items-center gap-2">
            <input type="checkbox" checked={Boolean(form.is_featured)}
              onChange={(e) => set('is_featured', e.target.checked)}
              className="h-4 w-4 rounded border-gray-300 text-indigo-600" />
            <span className="text-sm text-gray-700">Mostrar en portada</span>
          </label>
        </Field>
      </div>

      <div className="flex justify-end gap-3 border-t border-gray-100 pt-4">
        <Button type="button" variant="secondary" onClick={onCancel}>Cancelar</Button>
        <Button type="submit" loading={loading}>{initial ? 'Actualizar' : 'Crear'} habilidad</Button>
      </div>
    </form>
  );
}

function inputCls(hasError: boolean) {
  return `w-full rounded-md border px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-indigo-500 ${hasError ? 'border-red-400' : 'border-gray-300'}`;
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
