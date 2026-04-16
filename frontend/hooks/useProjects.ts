'use client';

import { useState, useEffect } from 'react';
import { getProjects } from '@/services/projects';
import type { Project, PaginationMeta } from '@/types';

interface UseProjectsParams {
  page?: number;
  category?: string;
  featured?: boolean;
}

interface UseProjectsState {
  projects: Project[];
  meta: PaginationMeta | null;
  loading: boolean;
  error: string | null;
}

export function useProjects(params: UseProjectsParams = {}) {
  const [state, setState] = useState<UseProjectsState>({
    projects: [],
    meta: null,
    loading: true,
    error: null,
  });

  useEffect(() => {
    let cancelled = false;

    setState((s) => ({ ...s, loading: true, error: null }));

    getProjects(params)
      .then(({ data, meta }) => {
        if (!cancelled) {
          setState({ projects: data, meta, loading: false, error: null });
        }
      })
      .catch((err) => {
        if (!cancelled) {
          const message =
            err instanceof Error ? err.message : 'Error al cargar proyectos';
          setState((s) => ({ ...s, loading: false, error: message }));
        }
      });

    return () => {
      cancelled = true;
    };
    // eslint-disable-next-line react-hooks/exhaustive-deps
  }, [params.page, params.category, params.featured]);

  return state;
}
