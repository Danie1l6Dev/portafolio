'use client';

import { useState, useEffect, useCallback } from 'react';
import {
  adminGetProjects, adminGetProject,
  adminCreateProject, adminUpdateProject, adminDeleteProject,
} from '@/services/projects';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Modal } from '@/components/admin/Modal';
import { ProjectForm } from '@/components/admin/ProjectForm';
import { statusLabel, statusColor } from '@/lib/utils';
import type { Project, ProjectPayload, PaginationMeta } from '@/types';

export default function AdminProyectosPage() {
  const [projects, setProjects] = useState<Project[]>([]);
  const [meta, setMeta] = useState<PaginationMeta | null>(null);
  const [page, setPage] = useState(1);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [modalOpen, setModalOpen] = useState(false);
  const [editing, setEditing] = useState<Project | undefined>();
  const [filterStatus, setFilterStatus] = useState('');

  const load = useCallback(async (p = page) => {
    setLoading(true);
    setError(null);
    try {
      const res = await adminGetProjects({ page: p, status: filterStatus || undefined });
      setProjects(res.data);
      setMeta(res.meta);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar');
    } finally {
      setLoading(false);
    }
  }, [page, filterStatus]);

  useEffect(() => { load(); }, [load]);

  function openCreate() { setEditing(undefined); setModalOpen(true); }

  async function openEdit(project: Project) {
    // Cargamos el proyecto completo (con description, skills, etc.)
    try {
      const full = await adminGetProject(project.id);
      setEditing(full);
      setModalOpen(true);
    } catch {
      setEditing(project);
      setModalOpen(true);
    }
  }

  function closeModal() { setModalOpen(false); setEditing(undefined); }

  async function handleSubmit(payload: ProjectPayload) {
    if (editing) {
      await adminUpdateProject(editing.id, payload);
    } else {
      await adminCreateProject(payload);
    }
    closeModal();
    load();
  }

  async function handleDelete(project: Project) {
    if (!confirm(`¿Eliminar el proyecto "${project.title}"? Esta acción es irreversible.`)) return;
    try {
      await adminDeleteProject(project.id);
      load();
    } catch (err) {
      alert(err instanceof Error ? err.message : 'Error al eliminar');
    }
  }

  const STATUS_FILTERS = [
    { label: 'Todos', value: '' },
    { label: 'Borrador', value: 'draft' },
    { label: 'Publicado', value: 'published' },
    { label: 'Archivado', value: 'archived' },
  ];

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Proyectos</h1>
          {meta && <p className="mt-0.5 text-sm text-gray-400">{meta.total} en total</p>}
        </div>
        <Button size="sm" onClick={openCreate}>+ Nuevo proyecto</Button>
      </div>

      {/* Filtro por estado */}
      <div className="mb-4 flex gap-2">
        {STATUS_FILTERS.map(({ label, value }) => (
          <button key={value} onClick={() => { setFilterStatus(value); setPage(1); }}
            className={`rounded-full border px-3 py-1 text-xs font-medium transition-colors ${
              filterStatus === value ? 'border-indigo-600 bg-indigo-600 text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'
            }`}>
            {label}
          </button>
        ))}
      </div>

      {error && <p className="mb-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">{error}</p>}

      {loading ? (
        <div className="space-y-2">{Array.from({ length: 6 }).map((_, i) => <div key={i} className="h-12 animate-pulse rounded-lg bg-gray-100" />)}</div>
      ) : (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white">
          <table className="w-full text-sm">
            <thead className="border-b border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-400">
              <tr>
                <th className="px-4 py-3 text-left">Título</th>
                <th className="px-4 py-3 text-left">Categoría</th>
                <th className="px-4 py-3 text-left">Estado</th>
                <th className="px-4 py-3 text-left">Destacado</th>
                <th className="px-4 py-3 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {projects.map((p) => (
                <tr key={p.id} className="hover:bg-gray-50">
                  <td className="px-4 py-3">
                    <div className="font-medium text-gray-900">{p.title}</div>
                    <div className="text-xs text-gray-400">{p.slug}</div>
                  </td>
                  <td className="px-4 py-3 text-gray-500">{p.category?.name ?? '—'}</td>
                  <td className="px-4 py-3">
                    <Badge variant="custom" colorClass={statusColor(p.status)}>{statusLabel(p.status)}</Badge>
                  </td>
                  <td className="px-4 py-3 text-center">
                    {p.is_featured ? <span title="Destacado">★</span> : <span className="text-gray-200">★</span>}
                  </td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="secondary" size="sm" onClick={() => openEdit(p)}>Editar</Button>
                      <Button variant="danger" size="sm" onClick={() => handleDelete(p)}>Eliminar</Button>
                    </div>
                  </td>
                </tr>
              ))}
              {projects.length === 0 && (
                <tr><td colSpan={5} className="px-4 py-8 text-center text-gray-400">
                  {filterStatus ? `No hay proyectos con estado "${filterStatus}".` : 'No hay proyectos.'}
                </td></tr>
              )}
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

      <Modal open={modalOpen} onClose={closeModal} title={editing ? 'Editar proyecto' : 'Nuevo proyecto'} size="xl">
        <ProjectForm initial={editing} onSubmit={handleSubmit} onCancel={closeModal} />
      </Modal>
    </div>
  );
}
