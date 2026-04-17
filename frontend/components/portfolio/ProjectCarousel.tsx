'use client';

import React, { useState, useEffect, useRef, useCallback } from 'react';
import Image from 'next/image';
import { useRouter } from 'next/navigation';
import { Badge } from '@/components/ui/Badge';
import { SkillIcon } from '@/components/portfolio/SkillIcon';
import { cn } from '@/lib/utils';
import type { Project } from '@/types';

// ── Tipos ─────────────────────────────────────────────────────

interface ProjectCarouselProps {
  projects: Project[];
}

interface SlideConfig {
  step: number;        // px entre centros de tarjetas
  visibleSide: number; // cuántas tarjetas se muestran a cada lado
}

const CAROUSEL_SCALE = 2;
const SIDE_OVERLAP_FACTOR = 0.5;
const TRACK_HEIGHT = 560;
const NAV_TOP = 270;

// ── Helpers ───────────────────────────────────────────────────

/** Distancia circular más corta desde activeIndex hasta index */
function circularOffset(index: number, active: number, total: number): number {
  let offset = index - active;
  if (offset > Math.floor(total / 2))  offset -= total;
  if (offset < -Math.floor(total / 2)) offset += total;
  return offset;
}

/** Estilos de transformación según distancia al centro */
function slideTransform(absOffset: number, translateX: number) {
  const scale   = absOffset === 0 ? 1 : absOffset === 1 ? 0.80 : 0.63;
  const opacity = absOffset === 0 ? 1 : absOffset === 1 ? 0.72 : 0.42;
  const zIndex  = 10 - absOffset * 3;
  return {
    transform: `translateX(calc(-50% + ${translateX}px)) scale(${scale})`,
    opacity,
    zIndex,
    transition: 'transform 0.45s cubic-bezier(0.4, 0, 0.2, 1), opacity 0.4s ease',
    transformOrigin: 'top center',
  } as React.CSSProperties;
}

// ── Componente principal ──────────────────────────────────────

export function ProjectCarousel({ projects }: ProjectCarouselProps) {
  const [activeIndex, setActiveIndex]   = useState(0);
  const [config, setConfig]             = useState<SlideConfig>({ step: 350 * CAROUSEL_SCALE, visibleSide: 2 });
  const containerRef                    = useRef<HTMLDivElement>(null);
  const touchStartX                     = useRef<number | null>(null);
  const router                          = useRouter();

  // Adapta step y visibleSide al ancho del contenedor
  useEffect(() => {
    const update = () => {
      const w = containerRef.current?.clientWidth ?? window.innerWidth;
      if (w < 480) {
        setConfig({ step: 230 * CAROUSEL_SCALE, visibleSide: 1 });
      } else if (w < 768) {
        setConfig({ step: 285 * CAROUSEL_SCALE, visibleSide: 1 });
      } else if (w < 1024) {
        setConfig({ step: 320 * CAROUSEL_SCALE, visibleSide: 2 });
      } else {
        setConfig({ step: 360 * CAROUSEL_SCALE, visibleSide: 2 });
      }
    };

    update();
    const ro = new ResizeObserver(update);
    if (containerRef.current) ro.observe(containerRef.current);
    return () => ro.disconnect();
  }, []);

  // Navegación
  const prev = useCallback(() => {
    setActiveIndex(i => (i - 1 + projects.length) % projects.length);
  }, [projects.length]);

  const next = useCallback(() => {
    setActiveIndex(i => (i + 1) % projects.length);
  }, [projects.length]);

  // Flechas del teclado
  useEffect(() => {
    const onKey = (e: KeyboardEvent) => {
      if (e.key === 'ArrowLeft')  prev();
      if (e.key === 'ArrowRight') next();
    };
    window.addEventListener('keydown', onKey);
    return () => window.removeEventListener('keydown', onKey);
  }, [prev, next]);

  // Swipe táctil
  const onTouchStart = (e: React.TouchEvent) => {
    touchStartX.current = e.touches[0].clientX;
  };
  const onTouchEnd = (e: React.TouchEvent) => {
    if (touchStartX.current === null) return;
    const delta = e.changedTouches[0].clientX - touchStartX.current;
    if (delta < -50) next();
    else if (delta > 50) prev();
    touchStartX.current = null;
  };

  if (projects.length === 0) return null;

  const { step, visibleSide } = config;
  const showDots = projects.length <= 12;

  return (
    <div ref={containerRef} className="relative w-full select-none">

      {/* ── Pista del carrusel ─────────────────────────────── */}
      <div
        className="relative overflow-hidden"
        style={{ height: TRACK_HEIGHT }}
        onTouchStart={onTouchStart}
        onTouchEnd={onTouchEnd}
        aria-label="Carrusel de proyectos"
      >
        {/* Máscaras de fade lateral para dar profundidad */}
        <div className="pointer-events-none absolute inset-y-0 left-0 z-10 w-20 bg-gradient-to-r from-white/90 to-transparent" />
        <div className="pointer-events-none absolute inset-y-0 right-0 z-10 w-20 bg-gradient-to-l from-white/90 to-transparent" />

        {projects.map((project, index) => {
          const offset    = circularOffset(index, activeIndex, projects.length);
          const absOffset = Math.abs(offset);

          if (absOffset > visibleSide) return null;

          const isActive = absOffset === 0;

          return (
            <div
              key={project.id}
              role="button"
              tabIndex={isActive ? 0 : -1}
              aria-label={isActive ? `Ver detalle: ${project.title}` : `Ir a ${project.title}`}
              className="absolute left-1/2 top-6 w-[32rem] cursor-pointer sm:w-[36rem] md:w-[40rem]"
              style={slideTransform(absOffset, offset * step * SIDE_OVERLAP_FACTOR)}
              onClick={() => {
                if (isActive) {
                  router.push(`/proyectos/${project.slug}`);
                } else {
                  setActiveIndex(index);
                }
              }}
              onKeyDown={(e) => {
                if (e.key === 'Enter' || e.key === ' ') {
                  e.preventDefault();
                  if (isActive) router.push(`/proyectos/${project.slug}`);
                  else setActiveIndex(index);
                }
              }}
            >
              <ProjectSlide project={project} isActive={isActive} />
            </div>
          );
        })}
      </div>

      {/* ── Botones de navegación ──────────────────────────── */}
      {projects.length > 1 && (
        <>
          <button
            onClick={prev}
            aria-label="Proyecto anterior"
            className="absolute left-3 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-md transition-all hover:border-sky-200 hover:bg-sky-50 hover:text-sky-600"
            style={{ top: NAV_TOP }}
          >
            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
              <path strokeLinecap="round" strokeLinejoin="round" d="M15 19l-7-7 7-7" />
            </svg>
          </button>

          <button
            onClick={next}
            aria-label="Siguiente proyecto"
            className="absolute right-3 z-20 flex h-10 w-10 -translate-y-1/2 items-center justify-center rounded-full border border-slate-200 bg-white text-slate-500 shadow-md transition-all hover:border-sky-200 hover:bg-sky-50 hover:text-sky-600"
            style={{ top: NAV_TOP }}
          >
            <svg className="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
              <path strokeLinecap="round" strokeLinejoin="round" d="M9 5l7 7-7 7" />
            </svg>
          </button>
        </>
      )}

      {/* ── Indicadores ───────────────────────────────────── */}
      <div className="mt-3 flex items-center justify-center gap-2">
        {showDots ? (
          projects.map((_, i) => (
            <button
              key={i}
              onClick={() => setActiveIndex(i)}
              aria-label={`Proyecto ${i + 1}`}
              className={cn(
                'h-1.5 rounded-full transition-all duration-300',
                i === activeIndex
                  ? 'w-6 bg-sky-600'
                  : 'w-1.5 bg-slate-300 hover:bg-slate-400',
              )}
            />
          ))
        ) : (
          <span className="text-xs tabular-nums text-slate-400">
            {activeIndex + 1} / {projects.length}
          </span>
        )}
      </div>
    </div>
  );
}

// ── Slide individual ──────────────────────────────────────────

function ProjectSlide({
  project,
  isActive,
}: {
  project: Project;
  isActive: boolean;
}) {
  return (
    <article
      className={cn(
        'overflow-hidden rounded-2xl border bg-white transition-shadow duration-300',
        isActive
          ? 'border-slate-200 shadow-2xl shadow-slate-300/50'
          : 'border-slate-100 shadow-sm',
      )}
    >
      {/* Cover */}
      <div className="relative aspect-video w-full overflow-hidden bg-slate-100">
        {project.cover_image ? (
          <Image
            src={project.cover_image}
            alt={project.title}
            fill
            className={cn(
              'object-cover transition-transform duration-500',
              isActive && 'group-hover:scale-105',
            )}
            sizes="(max-width: 640px) 80vw, (max-width: 1024px) 45vw, 380px"
          />
        ) : (
          <div className="flex h-full items-center justify-center text-slate-200">
            <svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.2">
              <rect x="2" y="3" width="20" height="14" rx="2" />
              <path d="M8 21h8M12 17v4" />
            </svg>
          </div>
        )}

        {/* Badge destacado */}
        {project.is_featured && (
          <span className="absolute left-3 top-3 z-10">
            <Badge variant="primary">Destacado</Badge>
          </span>
        )}

        {/* En progreso */}
        {project.in_progress && (
          <span className="absolute right-3 top-3 z-10">
            <Badge variant="warning">En progreso</Badge>
          </span>
        )}

        {/* CTA visible solo en la tarjeta activa */}
        {isActive && (
          <div className="absolute inset-0 flex items-end bg-gradient-to-t from-slate-900/55 via-transparent to-transparent">
            <span className="m-4 inline-flex items-center gap-1.5 rounded-lg bg-white/90 px-3 py-1.5 text-xs font-semibold text-slate-800 backdrop-blur-sm">
              Ver proyecto
              <svg className="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth={2.5}>
                <path strokeLinecap="round" strokeLinejoin="round" d="M17 8l4 4m0 0l-4 4m4-4H3" />
              </svg>
            </span>
          </div>
        )}
      </div>

      {/* Contenido */}
      <div className="p-4">
        {/* Categoría */}
        {project.category && (
          <Badge
            variant="custom"
            colorClass={project.category.color ? undefined : 'bg-slate-100 text-slate-600'}
            style={
              project.category.color
                ? {
                    backgroundColor: `${project.category.color}18`,
                    color: project.category.color,
                  }
                : undefined
            }
          >
            {project.category.name}
          </Badge>
        )}

        {/* Título */}
        <h3 className="mt-2 line-clamp-1 text-base font-semibold leading-snug text-slate-900">
          {project.title}
        </h3>

        {/* Resumen */}
        <p className="mt-1 line-clamp-2 text-sm leading-relaxed text-slate-500">
          {project.summary}
        </p>

        {/* Skills — solo en la tarjeta activa */}
        {isActive && project.skills && project.skills.length > 0 && (
          <div className="mt-3 flex flex-wrap gap-1">
            {project.skills.slice(0, 3).map((skill) => (
              <Badge key={skill.id} variant="default" className="text-xs">
                {skill.icon && <SkillIcon icon={skill.icon} name={skill.name} size="sm" className="mr-1" />}
                {skill.name}
              </Badge>
            ))}
            {project.skills.length > 3 && (
              <Badge variant="default" className="text-xs text-slate-400">
                +{project.skills.length - 3}
              </Badge>
            )}
          </div>
        )}
      </div>
    </article>
  );
}
