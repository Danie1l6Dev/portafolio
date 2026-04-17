'use client';

import { useState, useEffect, useCallback } from 'react';
import { adminGetSkills, adminCreateSkill, adminUpdateSkill, adminDeleteSkill } from '@/services/skills';
import { Button } from '@/components/ui/Button';
import { Badge } from '@/components/ui/Badge';
import { Modal } from '@/components/admin/Modal';
import { SkillForm } from '@/components/admin/SkillForm';
import { skillLevelLabel } from '@/lib/utils';
import type { Skill, SkillPayload } from '@/types';

export default function AdminHabilidadesPage() {
  const [skills, setSkills] = useState<Skill[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [modalOpen, setModalOpen] = useState(false);
  const [editing, setEditing] = useState<Skill | undefined>();
  const [filterGroup, setFilterGroup] = useState<string>('');

  const load = useCallback(async () => {
    setLoading(true);
    setError(null);
    try {
      const data = await adminGetSkills();
      setSkills(data);
    } catch (err) {
      setError(err instanceof Error ? err.message : 'Error al cargar');
    } finally {
      setLoading(false);
    }
  }, []);

  useEffect(() => { load(); }, [load]);

  const groups = [...new Set(skills.map((s) => s.group).filter(Boolean))] as string[];
  const filtered = filterGroup ? skills.filter((s) => s.group === filterGroup) : skills;

  function openCreate() { setEditing(undefined); setModalOpen(true); }
  function openEdit(skill: Skill) { setEditing(skill); setModalOpen(true); }
  function closeModal() { setModalOpen(false); setEditing(undefined); }

  async function handleSubmit(payload: SkillPayload) {
    if (editing) {
      await adminUpdateSkill(editing.id, payload);
    } else {
      await adminCreateSkill(payload);
    }
    closeModal();
    load();
  }

  async function handleDelete(skill: Skill) {
    if (!confirm(`¿Eliminar la habilidad "${skill.name}"?`)) return;
    try {
      await adminDeleteSkill(skill.id);
      setSkills((prev) => prev.filter((s) => s.id !== skill.id));
    } catch (err) {
      alert(err instanceof Error ? err.message : 'Error al eliminar');
    }
  }

  return (
    <div>
      <div className="mb-6 flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold text-gray-900">Habilidades</h1>
          <p className="mt-0.5 text-sm text-gray-400">{skills.length} en total</p>
        </div>
        <Button size="sm" onClick={openCreate}>+ Nueva habilidad</Button>
      </div>

      {/* Filtro por grupo */}
      {groups.length > 0 && (
        <div className="mb-4 flex flex-wrap gap-2">
          <button onClick={() => setFilterGroup('')}
            className={`rounded-full border px-3 py-1 text-xs font-medium transition-colors ${!filterGroup ? 'border-sky-600 bg-sky-600 text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'}`}>
            Todos
          </button>
          {groups.map((g) => (
            <button key={g} onClick={() => setFilterGroup(g)}
              className={`rounded-full border px-3 py-1 text-xs font-medium transition-colors ${filterGroup === g ? 'border-sky-600 bg-sky-600 text-white' : 'border-gray-200 text-gray-600 hover:bg-gray-50'}`}>
              {g}
            </button>
          ))}
        </div>
      )}

      {error && <p className="mb-4 rounded-md bg-red-50 px-3 py-2 text-sm text-red-600">{error}</p>}

      {loading ? (
        <div className="space-y-2">{Array.from({ length: 6 }).map((_, i) => <div key={i} className="h-12 animate-pulse rounded-lg bg-gray-100" />)}</div>
      ) : (
        <div className="overflow-hidden rounded-xl border border-gray-200 bg-white">
          <table className="w-full text-sm">
            <thead className="border-b border-gray-100 bg-gray-50 text-xs uppercase tracking-wide text-gray-400">
              <tr>
                <th className="px-4 py-3 text-left">Nombre</th>
                <th className="px-4 py-3 text-left">Grupo</th>
                <th className="px-4 py-3 text-left">Nivel</th>
                <th className="px-4 py-3 text-left">Orden</th>
                <th className="px-4 py-3 text-left">Destacada</th>
                <th className="px-4 py-3 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody className="divide-y divide-gray-100">
              {filtered.map((skill) => (
                <tr key={skill.id} className="hover:bg-gray-50">
                  <td className="px-4 py-3 font-medium text-gray-900">
                    <span className="flex items-center gap-2">
                      {skill.icon && <span>{skill.icon}</span>}
                      {skill.name}
                    </span>
                  </td>
                  <td className="px-4 py-3 text-gray-500">{skill.group ?? '—'}</td>
                  <td className="px-4 py-3 text-gray-600">{skill.level} — {skillLevelLabel(skill.level)}</td>
                  <td className="px-4 py-3 text-gray-400">{skill.sort_order}</td>
                  <td className="px-4 py-3">
                    {skill.is_featured ? <Badge variant="primary">Sí</Badge> : <span className="text-gray-300">—</span>}
                  </td>
                  <td className="px-4 py-3 text-right">
                    <div className="flex justify-end gap-2">
                      <Button variant="secondary" size="sm" onClick={() => openEdit(skill)}>Editar</Button>
                      <Button variant="danger" size="sm" onClick={() => handleDelete(skill)}>Eliminar</Button>
                    </div>
                  </td>
                </tr>
              ))}
              {filtered.length === 0 && <tr><td colSpan={6} className="px-4 py-8 text-center text-gray-400">No hay habilidades.</td></tr>}
            </tbody>
          </table>
        </div>
      )}

      <Modal open={modalOpen} onClose={closeModal} title={editing ? 'Editar habilidad' : 'Nueva habilidad'}>
        <SkillForm initial={editing} onSubmit={handleSubmit} onCancel={closeModal} />
      </Modal>
    </div>
  );
}
