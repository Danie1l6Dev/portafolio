'use client';

import { FormEvent, useEffect, useRef, useState } from 'react';
import { Button } from '@/components/ui/Button';
import { ApiError } from '@/services/api';
import { adminGetCategories } from '@/services/categories';
import { adminGetSkills } from '@/services/skills';
import { SkillIcon } from '@/components/portfolio/SkillIcon';
import type { Category, Project, ProjectPayload, ProjectStatus, Skill } from '@/types';

interface ProjectFormProps {
  initial?: Project;
  onSubmit: (payload: ProjectPayload) => Promise<void>;
  onCancel: () => void;
}

type FormState = {
  title: string; summary: string; description: string;
  demo_url: string; repo_url: string;
  status: ProjectStatus; is_featured: boolean; sort_order: number;
  started_at: string; finished_at: string; in_progress: boolean;
  category_id: string; skill_ids: number[];
  cover_image?: File;
};

const EMPTY: FormState = {
  title: '', summary: '', description: '', demo_url: '', repo_url: '',
  status: 'draft', is_featured: false, sort_order: 0,
  started_at: '', finished_at: '', in_progress: false,
  category_id: '', skill_ids: [],
};

export function ProjectForm({ initial, onSubmit, onCancel }: ProjectFormProps) {
  const [form, setForm] = useState<FormState>(EMPTY);
  const [loading, setLoading] = useState(false);
  const [errors, setErrors] = useState<Record<string, string>>({});
  const [globalError, setGlobalError] = useState('');
  const [coverPreview, setCoverPreview] = useState<string | null>(null);
  const [categories, setCategories] = useState<Category[]>([]);
  const [skills, setSkills] = useState<Skill[]>([]);
  const fileRef = useRef<HTMLInputElement>(null);

  // Cargar categorías y skills para los selectores
  useEffect(() => {
    adminGetCategories().then((r) => setCategories(r.data)).catch(() => {});
    adminGetSkills().then((res) => setSkills(res.data)).catch(() => {});
  }, []);

  useEffect(() => {
    if (initial) {
      setForm({
        title: initial.title, summary: initial.summary,
        description: initial.description ?? '',
        demo_url: initial.demo_url ?? '', repo_url: initial.repo_url ?? '',
        status: initial.status, is_featured: initial.is_featured,
        sort_order: initial.sort_order,
        started_at: initial.started_at?.slice(0, 10) ?? '',
        finished_at: initial.finished_at?.slice(0, 10) ?? '',
        in_progress: initial.in_progress,
        category_id: initial.category?.id ? String(initial.category.id) : '',
        skill_ids: initial.skills?.map((s) => s.id) ?? [],
      });
      setCoverPreview(initial.cover_image ?? null);
    } else {
      setForm(EMPTY);
      setCoverPreview(null);
    }
  }, [initial]);

  function set(key: keyof FormState, value: unknown) {
    setForm((f) => ({ ...f, [key]: value }));
    setErrors((e) => ({ ...e, [key]: '' }));
  }

  function toggleSkill(id: number) {
    setForm((f) => ({
      ...f,
      skill_ids: f.skill_ids.includes(id)
        ? f.skill_ids.filter((s) => s !== id)
        : [...f.skill_ids, id],
    }));
  }

  function handleCover(e: React.ChangeEvent<HTMLInputElement>) {
    const file = e.target.files?.[0];
    if (!file) return;
    set('cover_image', file);
    setCoverPreview(URL.createObjectURL(file));
  }

  async function handleSubmit(e: FormEvent) {
    e.preventDefault();
    setLoading(true);
    setErrors({});
    setGlobalError('');
    try {
      const payload: ProjectPayload = {
        title: form.title, summary: form.summary,
        description: form.description || null,
        demo_url: form.demo_url || null, repo_url: form.repo_url || null,
        status: form.status, is_featured: form.is_featured,
        sort_order: Number(form.sort_order),
        started_at: form.started_at || null,
        finished_at: form.in_progress ? null : (form.finished_at || null),
        category_id: form.category_id ? Number(form.category_id) : null,
        skill_ids: form.skill_ids,
        ...(form.cover_image ? { cover_image: form.cover_image } : {}),
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

  // Agrupar skills por grupo
  const skillGroups = skills.reduce<Record<string, Skill[]>>((acc, s) => {
    const g = s.group ?? 'Sin grupo';
    if (!acc[g]) acc[g] = [];
    acc[g].push(s);
    return acc;
  }, {});

  return (
    <form onSubmit={handleSubmit} className="space-y-5">
      {globalError && <p className="rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">{globalError}</p>}

      {/* Título */}
      <Field label="Título *" error={errors.title}>
        <input type="text" required value={form.title} onChange={(e) => set('title', e.target.value)}
          className={inputCls(!!errors.title)} placeholder="Nombre del proyecto" />
      </Field>

      {/* Resumen */}
      <Field label="Resumen *" error={errors.summary}>
        <textarea rows={2} required value={form.summary} onChange={(e) => set('summary', e.target.value)}
          className={inputCls(!!errors.summary)} placeholder="Descripción breve (aparece en las cards)" />
      </Field>

      {/* Descripción larga */}
      <Field label="Descripción completa" error={errors.description}>
        <textarea rows={5} value={form.description} onChange={(e) => set('description', e.target.value)}
          className={inputCls(!!errors.description)} placeholder="Descripción detallada del proyecto..." />
      </Field>

      {/* URLs */}
      <div className="grid grid-cols-2 gap-4">
        <Field label="URL Demo" error={errors.demo_url}>
          <input type="url" value={form.demo_url} onChange={(e) => set('demo_url', e.target.value)}
            className={inputCls(!!errors.demo_url)} placeholder="https://demo.ejemplo.com" />
        </Field>
        <Field label="URL Repositorio" error={errors.repo_url}>
          <input type="url" value={form.repo_url} onChange={(e) => set('repo_url', e.target.value)}
            className={inputCls(!!errors.repo_url)} placeholder="https://github.com/..." />
        </Field>
      </div>

      {/* Estado + Categoría */}
      <div className="grid grid-cols-2 gap-4">
        <Field label="Estado" error={errors.status}>
          <select value={form.status} onChange={(e) => set('status', e.target.value as ProjectStatus)}
            className={inputCls(!!errors.status)}>
            <option value="draft">Borrador</option>
            <option value="published">Publicado</option>
            <option value="archived">Archivado</option>
          </select>
        </Field>
        <Field label="Categoría" error={errors.category_id}>
          <select value={form.category_id} onChange={(e) => set('category_id', e.target.value)}
            className={inputCls(!!errors.category_id)}>
            <option value="">Sin categoría</option>
            {categories.map((cat) => (
              <option key={cat.id} value={cat.id}>{cat.name}</option>
            ))}
          </select>
        </Field>
      </div>

      {/* Fechas */}
      <div className="grid grid-cols-2 gap-4">
        <Field label="Fecha inicio" error={errors.started_at}>
          <input type="date" value={form.started_at} onChange={(e) => set('started_at', e.target.value)}
            className={inputCls(!!errors.started_at)} />
        </Field>
        <Field label="Fecha fin" error={errors.finished_at}>
          <input type="date" value={form.finished_at} disabled={form.in_progress}
            onChange={(e) => set('finished_at', e.target.value)}
            className={`${inputCls(!!errors.finished_at)} ${form.in_progress ? 'cursor-not-allowed opacity-50' : ''}`} />
        </Field>
      </div>

      {/* Opciones booleanas */}
      <div className="flex flex-wrap gap-6">
        <label className="flex cursor-pointer items-center gap-2">
          <input type="checkbox" checked={form.in_progress} onChange={(e) => set('in_progress', e.target.checked)}
            className="h-4 w-4 rounded border-gray-300 text-sky-600" />
          <span className="text-sm text-gray-700">En progreso</span>
        </label>
        <label className="flex cursor-pointer items-center gap-2">
          <input type="checkbox" checked={form.is_featured} onChange={(e) => set('is_featured', e.target.checked)}
            className="h-4 w-4 rounded border-gray-300 text-sky-600" />
          <span className="text-sm text-gray-700">Proyecto destacado</span>
        </label>
        <div className="flex items-center gap-2">
          <span className="text-sm text-gray-700">Orden:</span>
          <input type="number" min={0} value={form.sort_order}
            onChange={(e) => set('sort_order', parseInt(e.target.value) || 0)}
            className="w-20 rounded-md border border-gray-300 px-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-sky-500" />
        </div>
      </div>

      {/* Imagen de portada */}
      <Field label="Imagen de portada" error={errors.cover_image}>
        <div className="flex items-start gap-4">
          {coverPreview && (
            <img src={coverPreview} alt="Cover" className="h-20 w-32 rounded-lg border border-gray-200 object-cover" />
          )}
          <div>
            <input ref={fileRef} type="file" accept="image/jpeg,image/png,image/webp" onChange={handleCover} className="hidden" />
            <Button type="button" variant="secondary" size="sm" onClick={() => fileRef.current?.click()}>
              {coverPreview ? 'Cambiar imagen' : 'Subir imagen'}
            </Button>
            <p className="mt-1 text-xs text-gray-400">JPG, PNG o WebP. Máx 2 MB.</p>
          </div>
        </div>
      </Field>

      {/* Habilidades */}
      {Object.keys(skillGroups).length > 0 && (
        <Field label="Tecnologías / Habilidades" error={errors.skill_ids}>
          <div className="max-h-48 overflow-y-auto rounded-md border border-gray-200 p-3 space-y-3">
            {Object.entries(skillGroups).map(([group, groupSkills]) => (
              <div key={group}>
                <p className="mb-1 text-xs font-semibold uppercase tracking-wide text-gray-400">{group}</p>
                <div className="flex flex-wrap gap-2">
                  {groupSkills.map((skill) => {
                    const checked = form.skill_ids.includes(skill.id);
                    return (
                      <label key={skill.id}
                        className={`flex cursor-pointer items-center gap-1.5 rounded-full border px-3 py-1 text-xs font-medium transition-colors ${
                          checked ? 'border-sky-500 bg-sky-50 text-sky-700' : 'border-gray-200 text-gray-600 hover:border-gray-300'
                        }`}>
                        <input type="checkbox" checked={checked} onChange={() => toggleSkill(skill.id)} className="sr-only" />
                        {skill.icon && <SkillIcon icon={skill.icon} name={skill.name} size="sm" />}
                        {skill.name}
                      </label>
                    );
                  })}
                </div>
              </div>
            ))}
          </div>
          <p className="mt-1 text-xs text-gray-400">{form.skill_ids.length} seleccionadas</p>
        </Field>
      )}

      <div className="flex justify-end gap-3 border-t border-gray-100 pt-4">
        <Button type="button" variant="secondary" onClick={onCancel}>Cancelar</Button>
        <Button type="submit" loading={loading}>{initial ? 'Actualizar' : 'Crear'} proyecto</Button>
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
