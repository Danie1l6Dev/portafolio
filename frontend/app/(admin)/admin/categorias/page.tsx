'use client';

import { useState, useEffect, useCallback } from 'react';
import {
  adminGetCategories,
  adminCreateCategory,
  adminUpdateCategory,
  adminDeleteCategory,
} from '@/services/categories';
import { Button } from '@/components/ui/Button';
import { Modal } from '@/components/admin/Modal';
import { CategoryForm } from '@/components/admin/CategoryForm';
import type { Category, CategoryPayload } from '@/types';

export default function AdminCategoriasPage() {
  const [categories, setCategories] = useState<Category[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);

  // Modal state
  const [modalOpen, setModalOpen] = useState(false);
  const [editing, setEditing] = useState<Category | undefined>();

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const res = await adminGetCategories();
      setCategories(res.data);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);

  function openCreate() { setEditing(undefined); setModalOpen(true); }
  function openEdit(cat: Category) { setEditing(cat); setModalOpen(true); }
  function closeModal() { setModalOpen(false); setEditing(undefined); }

  async function handleSubmit(payload: CategoryPayload) {
    if (editing) {
      await adminUpdateCategory(editing.id, payload);
    } else {
      await adminCreateCategory(payload);
    }
    closeModal();
    load();
  }

  async function handleDelete(cat: Category) {
    if (!confirm(`¿Eliminar la categoría "${cat.name}"? Esta acción es irreversible.`)) return;
    try {
      await adminDeleteCategory(cat.id);
      load();
    } catch (err) {
      alert(err instanceof Error ? err.message : 'Error al eliminar');
    }
  }

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Categorías</h1>
          <p className="mt-0.5 text-sm text-gray-400">{categories.length} en total</p>
        </div>
        <Button size="sm" onClick={openCreate}>+ Nueva categoría</Button>
      </div>

      {error && <p className="mb-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">{error}</p>}

      {loading ? (
        <Skeleton rows={5} cols={5} />
      ) : (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white">
          <table className="w-full text-sm">
            <thead className="border-b border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-400">
              <tr>
                <th className="px-4 py-3 text-left">Nombre</th>
                <th className="px-4 py-3 text-left">Slug</th>
                <th className="px-4 py-3 text-left">Color</th>
                <th className="px-4 py-3 text-left">Proyectos</th>
                <th className="px-4 py-3 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {categories.map((cat) => (
                <tr key={cat.id} className="hover:bg-gray-50">
                  <td className="px-4 py-3 font-medium text-gray-900">{cat.name}</td>
                  <td className="px-4 py-3 font-mono text-xs text-gray-400">{cat.slug}</td>
                  <td className="px-4 py-3">
                    {cat.color ? (
                      <span className="flex items-center gap-2">
                        <span className="h-4 w-4 rounded-full border border-gray-200" style={{ backgroundColor: cat.color }} />
                        <span className="font-mono text-xs text-gray-400">{cat.color}</span>
                      </span>
                    ) : '—'}
                  </td>
                  <td className="px-4 py-3 text-gray-500">{cat.projects_count ?? '—'}</td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="secondary" size="sm" onClick={() => openEdit(cat)}>Editar</Button>
                      <Button variant="danger" size="sm" onClick={() => handleDelete(cat)}>Eliminar</Button>
                    </div>
                  </td>
                </tr>
              ))}
              {categories.length === 0 && (
                <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-400">No hay categorías.</td></tr>
              )}
            </tbody>
          </table>
        </div>
      )}

      <Modal
        open={modalOpen}
        onClose={closeModal}
        title={editing ? 'Editar categoría' : 'Nueva categoría'}
      >
        <CategoryForm
          initial={editing}
          onSubmit={handleSubmit}
          onCancel={closeModal}
        />
      </Modal>
    </div>
  );
}

// ── Helpers ───────────────────────────────────────────────────

function Skeleton({ rows, cols }: { rows: number; cols: number }) {
  return (
    <div className="space-y-2">
      {Array.from({ length: rows }).map((_, i) => (
        <div key={i} className="flex gap-2">
          {Array.from({ length: cols }).map((_, j) => (
            <div key={j} className="h-10 flex-1 animate-pulse rounded bg-gray-100" />
          ))}
        </div>
      ))}
    </div>
  );
}

