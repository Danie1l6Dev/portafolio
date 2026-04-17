'use client';

import { useState, useEffect, useCallback } from 'react';
import { Button } from '@/components/ui/Button';
import { cn } from '@/lib/utils';
import {
  adminGetMessages,
  adminMarkMessageRead,
  adminMarkAllMessagesRead,
  adminDeleteMessage,
  type Message,
  type MessageFilter,
} from '@/services/messages';

// ── Helpers ───────────────────────────────────────────────────

function formatDate(iso: string): string {
  const d = new Date(iso);
  const now = new Date();
  const diffMs = now.getTime() - d.getTime();
  const diffMin = Math.floor(diffMs / 60_000);
  const diffH   = Math.floor(diffMs / 3_600_000);
  const diffD   = Math.floor(diffMs / 86_400_000);

  if (diffMin < 1)  return 'Ahora';
  if (diffMin < 60) return `Hace ${diffMin} min`;
  if (diffH   < 24) return `Hace ${diffH}h`;
  if (diffD   < 7)  return `Hace ${diffD}d`;

  return d.toLocaleDateString('es-CO', {
    day: '2-digit', month: 'short', year: 'numeric',
  });
}

function formatFullDate(iso: string): string {
  return new Date(iso).toLocaleString('es-CO', {
    weekday: 'long', day: '2-digit', month: 'long',
    year: 'numeric', hour: '2-digit', minute: '2-digit',
  });
}

// ── Componente principal ──────────────────────────────────────

export default function MensajesPage() {
  const [messages,    setMessages]    = useState<Message[]>([]);
  const [meta,        setMeta]        = useState<{ current_page: number; last_page: number; total: number; unread_count: number } | null>(null);
  const [selected,    setSelected]    = useState<Message | null>(null);
  const [filter,      setFilter]      = useState<MessageFilter>('all');
  const [page,        setPage]        = useState(1);
  const [loading,     setLoading]     = useState(true);
  const [actionLoading, setActionLoading] = useState(false);
  const [error,       setError]       = useState('');

  // ── Carga ───────────────────────────────────────────────────

  const load = useCallback(async () => {
    setLoading(true);
    setError('');
    try {
      const res = await adminGetMessages({ page, filter });
      setMessages(res.data);
      setMeta(res.meta);
      // Mantiene la selección actualizada si sigue en la lista
      if (selected) {
        const updated = res.data.find(m => m.id === selected.id);
        if (updated) setSelected(updated);
      }
    } catch {
      setError('No se pudieron cargar los mensajes.');
    } finally {
      setLoading(false);
    }
  }, [page, filter]); // eslint-disable-line react-hooks/exhaustive-deps

  useEffect(() => { load(); }, [load]);

  // ── Al seleccionar un mensaje, lo marca como leído ──────────

  async function handleSelect(msg: Message) {
    setSelected(msg);
    if (!msg.is_read) {
      const updated = await adminMarkMessageRead(msg.id);
      setMessages(prev => prev.map(m => m.id === updated.id ? updated : m));
      setSelected(updated);
      if (meta) setMeta({ ...meta, unread_count: Math.max(0, meta.unread_count - 1) });
    }
  }

  // ── Marcar todos como leídos ────────────────────────────────

  async function handleMarkAllRead() {
    setActionLoading(true);
    try {
      await adminMarkAllMessagesRead();
      await load();
      if (selected) setSelected({ ...selected, is_read: true });
    } finally {
      setActionLoading(false);
    }
  }

  // ── Eliminar ────────────────────────────────────────────────

  async function handleDelete(id: number) {
    if (!confirm('¿Eliminar este mensaje permanentemente?')) return;
    setActionLoading(true);
    try {
      await adminDeleteMessage(id);
      setMessages(prev => prev.filter(m => m.id !== id));
      if (selected?.id === id) setSelected(null);
      if (meta) setMeta({ ...meta, total: meta.total - 1 });
    } finally {
      setActionLoading(false);
    }
  }

  // ── Cambio de filtro ────────────────────────────────────────

  function changeFilter(f: MessageFilter) {
    setFilter(f);
    setPage(1);
    setSelected(null);
  }

  // ── Render ──────────────────────────────────────────────────

  return (
    <div className="flex h-[calc(100vh-4rem)] flex-col">
      {/* Header de la página */}
      <div className="mb-6 flex flex-wrap items-center justify-between gap-4">
        <div>
          <h1 className="text-xl font-bold text-slate-900">Mensajes</h1>
          <p className="mt-0.5 text-sm text-slate-500">
            {meta
              ? `${meta.total} mensaje${meta.total !== 1 ? 's' : ''} · ${meta.unread_count} sin leer`
              : 'Cargando…'}
          </p>
        </div>

        <div className="flex items-center gap-2">
          {/* Filtros */}
          <div className="flex rounded-lg border border-slate-200 bg-white p-0.5">
            {(['all', 'unread'] as MessageFilter[]).map(f => (
              <button
                key={f}
                onClick={() => changeFilter(f)}
                className={cn(
                  'rounded-md px-3 py-1.5 text-xs font-medium transition-all',
                  filter === f
                    ? 'bg-sky-600 text-white shadow-sm'
                    : 'text-slate-500 hover:text-slate-900',
                )}
              >
                {f === 'all' ? 'Todos' : 'Sin leer'}
                {f === 'unread' && meta && meta.unread_count > 0 && (
                  <span className="ml-1.5 rounded-full bg-white/20 px-1.5 py-0.5 text-[10px] font-bold">
                    {meta.unread_count}
                  </span>
                )}
              </button>
            ))}
          </div>

          {/* Marcar todos leídos */}
          {meta && meta.unread_count > 0 && (
            <Button
              variant="secondary"
              size="sm"
              loading={actionLoading}
              onClick={handleMarkAllRead}
            >
              Marcar todos leídos
            </Button>
          )}
        </div>
      </div>

      {/* Layout dividido: lista | detalle */}
      <div className="flex flex-1 gap-4 overflow-hidden">

        {/* ── Lista ──────────────────────────────────────────── */}
        <div className="flex w-80 flex-shrink-0 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white">
          {loading ? (
            <div className="space-y-0 divide-y divide-slate-100">
              {Array.from({ length: 6 }).map((_, i) => (
                <div key={i} className="flex gap-3 p-4">
                  <div className="h-8 w-8 flex-shrink-0 animate-pulse rounded-full bg-slate-100" />
                  <div className="flex-1 space-y-2">
                    <div className="h-3 w-3/4 animate-pulse rounded bg-slate-100" />
                    <div className="h-2.5 w-1/2 animate-pulse rounded bg-slate-100" />
                  </div>
                </div>
              ))}
            </div>
          ) : error ? (
            <p className="p-5 text-sm text-red-500">{error}</p>
          ) : messages.length === 0 ? (
            <div className="flex flex-1 flex-col items-center justify-center gap-2 p-8 text-center">
              <div className="flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-2xl">
                ✉
              </div>
              <p className="text-sm text-slate-400">
                {filter === 'unread' ? 'No hay mensajes sin leer.' : 'No hay mensajes todavía.'}
              </p>
            </div>
          ) : (
            <>
              <ul className="flex-1 divide-y divide-slate-100 overflow-y-auto">
                {messages.map(msg => (
                  <li key={msg.id}>
                    <button
                      type="button"
                      onClick={() => handleSelect(msg)}
                      className={cn(
                        'group w-full px-4 py-3.5 text-left transition-colors',
                        selected?.id === msg.id
                          ? 'bg-sky-50'
                          : 'hover:bg-slate-50',
                      )}
                    >
                      <div className="flex items-start gap-2.5">
                        {/* Avatar inicial */}
                        <div className={cn(
                          'flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full text-xs font-bold',
                          msg.is_read
                            ? 'bg-slate-100 text-slate-500'
                            : 'bg-sky-100 text-sky-700',
                        )}>
                          {msg.name.charAt(0).toUpperCase()}
                        </div>

                        <div className="min-w-0 flex-1">
                          <div className="flex items-center justify-between gap-2">
                            <span className={cn(
                              'truncate text-sm',
                              msg.is_read ? 'font-medium text-slate-700' : 'font-semibold text-slate-900',
                            )}>
                              {msg.name}
                            </span>
                            <span className="flex-shrink-0 text-[11px] text-slate-400">
                              {formatDate(msg.created_at)}
                            </span>
                          </div>

                          {msg.subject && (
                            <p className={cn(
                              'truncate text-xs',
                              msg.is_read ? 'text-slate-500' : 'font-medium text-slate-700',
                            )}>
                              {msg.subject}
                            </p>
                          )}

                          <p className="mt-0.5 truncate text-xs text-slate-400">
                            {msg.body}
                          </p>
                        </div>

                        {/* Indicador no leído */}
                        {!msg.is_read && (
                          <span className="mt-1 h-2 w-2 flex-shrink-0 rounded-full bg-sky-500" />
                        )}
                      </div>
                    </button>
                  </li>
                ))}
              </ul>

              {/* Paginación */}
              {meta && meta.last_page > 1 && (
                <div className="flex items-center justify-between border-t border-slate-100 px-4 py-2.5">
                  <button
                    disabled={page === 1}
                    onClick={() => setPage(p => p - 1)}
                    className="text-xs text-slate-500 hover:text-slate-900 disabled:opacity-40"
                  >
                    ← Anterior
                  </button>
                  <span className="text-xs text-slate-400">{page} / {meta.last_page}</span>
                  <button
                    disabled={page === meta.last_page}
                    onClick={() => setPage(p => p + 1)}
                    className="text-xs text-slate-500 hover:text-slate-900 disabled:opacity-40"
                  >
                    Siguiente →
                  </button>
                </div>
              )}
            </>
          )}
        </div>

        {/* ── Panel de detalle ────────────────────────────────── */}
        <div className="flex flex-1 flex-col overflow-hidden rounded-xl border border-slate-200 bg-white">
          {!selected ? (
            <div className="flex flex-1 flex-col items-center justify-center gap-3 text-center">
              <div className="flex h-14 w-14 items-center justify-center rounded-full bg-slate-100 text-3xl text-slate-300">
                ✉
              </div>
              <p className="text-sm text-slate-400">Selecciona un mensaje para leerlo</p>
            </div>
          ) : (
            <>
              {/* Header del detalle */}
              <div className="flex items-start justify-between border-b border-slate-100 px-6 py-4">
                <div className="min-w-0 flex-1">
                  <h2 className="text-base font-semibold text-slate-900 leading-snug">
                    {selected.subject || '(Sin asunto)'}
                  </h2>
                  <div className="mt-1 flex flex-wrap items-center gap-x-3 gap-y-0.5 text-xs text-slate-500">
                    <span className="font-medium text-slate-700">{selected.name}</span>
                    <a
                      href={`mailto:${selected.email}`}
                      className="text-sky-600 hover:underline"
                    >
                      {selected.email}
                    </a>
                    <span>{formatFullDate(selected.created_at)}</span>
                  </div>
                </div>

                {/* Acciones */}
                <div className="ml-4 flex flex-shrink-0 items-center gap-2">
                  <a
                    href={`https://mail.google.com/mail/?view=cm&to=${encodeURIComponent(selected.email)}&su=${encodeURIComponent(`Re: ${selected.subject ?? ''}`)}`}
                    target="_blank"
                    rel="noopener noreferrer"
                    className="inline-flex h-8 items-center gap-1.5 rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-slate-700 shadow-sm transition-all hover:border-sky-300 hover:bg-sky-50 hover:text-sky-700"
                  >
                    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" />
                    </svg>
                    Responder
                  </a>
                  <button
                    onClick={() => handleDelete(selected.id)}
                    disabled={actionLoading}
                    className="inline-flex h-8 items-center gap-1.5 rounded-lg border border-red-100 bg-red-50 px-3 text-xs font-medium text-red-600 transition-all hover:bg-red-100 disabled:opacity-50"
                    title="Eliminar mensaje"
                  >
                    <svg className="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2}>
                      <path strokeLinecap="round" strokeLinejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                    Eliminar
                  </button>
                </div>
              </div>

              {/* Cuerpo del mensaje */}
              <div className="flex-1 overflow-y-auto px-6 py-6">
                <div className="max-w-prose">
                  <p className="whitespace-pre-wrap text-sm leading-relaxed text-slate-700">
                    {selected.body}
                  </p>
                </div>
              </div>

              {/* Footer con metadatos */}
              <div className="border-t border-slate-100 px-6 py-3">
                <div className="flex flex-wrap items-center gap-x-5 gap-y-1 text-xs text-slate-400">
                  {selected.is_read ? (
                    <span className="flex items-center gap-1">
                      <span className="h-1.5 w-1.5 rounded-full bg-emerald-400" />
                      Leído{selected.read_at ? ` · ${formatDate(selected.read_at)}` : ''}
                    </span>
                  ) : (
                    <span className="flex items-center gap-1">
                      <span className="h-1.5 w-1.5 rounded-full bg-sky-400" />
                      Sin leer
                    </span>
                  )}
                  {selected.ip_address && (
                    <span>IP: {selected.ip_address}</span>
                  )}
                  <span>ID: #{selected.id}</span>
                </div>
              </div>
            </>
          )}
        </div>
      </div>
    </div>
  );
}
