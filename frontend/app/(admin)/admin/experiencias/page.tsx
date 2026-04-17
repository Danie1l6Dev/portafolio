'use client';

import { useState, useEffect, useCallback } from 'react';
import {
  adminGetExperiences, adminCreateExperience,
  adminUpdateExperience, adminDeleteExperience,
} from '@/services/experiences';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Modal } from '@/components/admin/Modal';
import { ExperienceForm } from '@/components/admin/ExperienceForm';
import { formatDateRange } from '@/lib/utils';
import type { Experience, ExperiencePayload, PaginationMeta } from '@/types';

export default function AdminExperienciasPage() {
  const [experiences, setExperiences] = useState<Experience[]>([]);
  const [meta, setMeta] = useState<PaginationMeta | null>(null);
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [modalOpen, setModalOpen] = useState(false);
  const [editing, setEditing] = useState<Experience | undefined>();

  const load = useCallback(async (p = page) => {
    setLoading(true);
    setError(null);
    try {
      const res = await adminGetExperiences({ page: p });
      setExperiences(res.data);
      setMeta(res.meta);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar');
    } finally {
      setLoading(false);
    }
  }, [page]);

  useEffect(() => { load(); }, [load]);

  function openCreate() { setEditing(undefined); setModalOpen(true); }
  function openEdit(exp: Experience) { setEditing(exp); setModalOpen(true); }
  function closeModal() { setModalOpen(false); setEditing(undefined); }

  async function handleSubmit(payload: ExperiencePayload) {
    if (editing) {
      await adminUpdateExperience(editing.id, payload);
    } else {
      await adminCreateExperience(payload);
    }
    closeModal();
    load();
  }

  async function handleDelete(exp: Experience) {
    if (!confirm(`¿Eliminar la experiencia en "${exp.company}"?`)) return;
    try {
      await adminDeleteExperience(exp.id);
      load();
    } catch (err) {
      alert(err instanceof Error ? err.message : 'Error al eliminar');
    }
  }

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Experiencias</h1>
          {meta && <p className="mt-0.5 text-sm text-gray-400">{meta.total} en total</p>}
        </div>
        <Button size="sm" onClick={openCreate}>+ Nueva experiencia</Button>
      </div>

      {error && <p className="mb-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">{error}</p>}

      {loading ? (
        <div className="space-y-2">{Array.from({ length: 4 }).map((_, i) => <div key={i} className="h-12 animate-pulse rounded-lg bg-gray-100" />)}</div>
      ) : (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white">
          <table className="w-full text-sm">
            <thead className="border-b border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-400">
              <tr>
                <th className="px-4 py-3 text-left">Empresa</th>
                <th className="px-4 py-3 text-left">Cargo</th>
                <th className="px-4 py-3 text-left">Período</th>
                <th className="px-4 py-3 text-left">Actual</th>
                <th className="px-4 py-3 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {experiences.map((exp) => (
                <tr key={exp.id} className="hover:bg-gray-50">
                  <td className="px-4 py-3 font-medium text-gray-900">{exp.company}</td>
                  <td className="px-4 py-3 text-gray-600">{exp.position}</td>
                  <td className="px-4 py-3 text-xs text-gray-400">
                    {formatDateRange(exp.started_at, exp.finished_at, exp.is_current)}
                  </td>
                  <td className="px-4 py-3">
                    {exp.is_current ? <Badge variant="success">Actual</Badge> : <span className="text-gray-300">—</span>}
                  </td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="secondary" size="sm" onClick={() => openEdit(exp)}>Editar</Button>
                      <Button variant="danger" size="sm" onClick={() => handleDelete(exp)}>Eliminar</Button>
                    </div>
                  </td>
                </tr>
              ))}
              {experiences.length === 0 && <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-400">No hay experiencias.</td></tr>}
            </tbody>
          </table>
        </div>
      )}

      {meta && meta.last_page > 1 && (
        <div className="mt-4 flex justify-end gap-2">
          <Button variant="secondary" size="sm" onClick={() => setPage((p) => p - 1)} disabled={page === 1}>← Anterior</Button>
          <span className="flex items-center px-2 text-sm text-gray-400">{page} / {meta.last_page}</span>
          <Button variant="secondary" size="sm" onClick={() => setPage((p) => p + 1)} disabled={page === meta.last_page}>Siguiente →</Button>
        </div>
      )}

      <Modal open={modalOpen} onClose={closeModal} title={editing ? 'Editar experiencia' : 'Nueva experiencia'} size="lg">
        <ExperienceForm initial={editing} onSubmit={handleSubmit} onCancel={closeModal} />
      </Modal>
    </div>
  );
}
