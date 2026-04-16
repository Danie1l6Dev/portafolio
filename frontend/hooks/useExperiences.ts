'use client';

import { useState, useEffect } from 'react';
import { getExperiences } from '@/services/experiences';
import type { Experience } from '@/types';

interface UseExperiencesState {
  experiences: Experience[];
  loading: boolean;
  error: string | null;
}

export function useExperiences() {
  const [state, setState] = useState<UseExperiencesState>({
    experiences: [],
    loading: true,
    error: null,
  });

  useEffect(() => {
    getExperiences()
      .then((data) =>
        setState({ experiences: data, loading: false, error: null }),
      )
      .catch((err) => {
        const message =
          err instanceof Error ? err.message : 'Error al cargar experiencias';
        setState((s) => ({ ...s, loading: false, error: message }));
      });
  }, []);

  return state;
}
