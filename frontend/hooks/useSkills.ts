'use client';

import { useState, useEffect } from 'react';
import { getSkills } from '@/services/skills';
import type { Skill } from '@/types';

interface UseSkillsState {
  skills: Skill[];
  groups: string[];
  loading: boolean;
  error: string | null;
}

export function useSkills() {
  const [state, setState] = useState<UseSkillsState>({
    skills: [],
    groups: [],
    loading: true,
    error: null,
  });

  useEffect(() => {
    getSkills()
      .then(({ data, meta }) =>
        setState({ skills: data, groups: meta.groups, loading: false, error: null }),
      )
      .catch((err) => {
        const message =
          err instanceof Error ? err.message : 'Error al cargar habilidades';
        setState((s) => ({ ...s, loading: false, error: message }));
      });
  }, []);

  return state;
}
