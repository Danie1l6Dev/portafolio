@extends('layouts.portfolio', [
    'title' => null,
    'description' => config('portfolio.description'),
    'canonical' => route('home'),
    'schema' => $schema,
])

@section('content')
    @php
        $services = collect(config('portfolio.services', []));
        $deliverySteps = collect(config('portfolio.delivery_steps', []));
    @endphp

    <section id="inicio" class="portfolio-section relative min-h-[calc(100svh-4.5rem)] overflow-hidden pt-16 sm:pt-24 lg:pt-20 xl:pt-24">
        <div class="portfolio-grid-bg absolute inset-0" aria-hidden="true"></div>
        <div class="portfolio-container relative grid items-center gap-14 pb-20 lg:grid-cols-[.98fr_1.02fr] lg:gap-16 lg:pb-20 xl:pb-24">
            <div data-reveal>
                <p class="portfolio-eyebrow mb-6">
                    <span class="size-2 rounded-full bg-emerald-500 shadow-[0_0_0_5px_rgba(16,185,129,.12)]" aria-hidden="true"></span>
                    {{ config('portfolio.availability') }}
                </p>

                <h1 class="max-w-4xl text-balance text-[clamp(3rem,4.6vw,5.1rem)] font-semibold leading-[0.94] tracking-[-0.06em] text-ink-950 dark:text-white">
                    {{ config('portfolio.hero_title') }}
                    <span class="text-signal-600 dark:text-signal-500">{{ config('portfolio.hero_highlight') }}</span>
                </h1>

                <p class="mt-7 max-w-2xl text-pretty text-lg leading-8 text-ink-600 dark:text-slate-300 sm:text-xl sm:leading-9">
                    {{ config('portfolio.intro') }}
                </p>

                <div class="mt-8 flex flex-wrap gap-3">
                    <a href="#contacto" class="portfolio-button">
                        Cuéntame tu proyecto
                        <span aria-hidden="true">↗</span>
                    </a>
                    <a href="#proyectos" class="portfolio-button portfolio-button--secondary">Ver trabajo realizado</a>
                </div>

                <div class="mt-10 flex flex-wrap items-center gap-x-8 gap-y-4 border-t border-ink-950/8 pt-6 dark:border-white/10">
                    <div>
                        <p class="font-mono text-xl font-semibold tabular-nums text-ink-950 dark:text-white">&lt; 48 h</p>
                        <p class="mt-1 text-xs text-ink-500 dark:text-slate-400">Respuesta habitual</p>
                    </div>
                    <div class="h-8 w-px bg-ink-950/10 dark:bg-white/12" aria-hidden="true"></div>
                    <div>
                        <p class="font-mono text-xl font-semibold tabular-nums text-ink-950 dark:text-white">{{ $featuredProjects->count() }}</p>
                        <p class="mt-1 text-xs text-ink-500 dark:text-slate-400">Casos destacados para revisar</p>
                    </div>
                </div>
            </div>

            <div class="relative mx-auto w-full max-w-2xl" data-reveal>
                <section class="service-panel" aria-labelledby="hero-services-title" data-service-panel>
                    <div class="service-panel__header">
                        <span class="font-mono text-[0.65rem] uppercase tracking-[0.18em] text-white/45">Servicios / desarrollo web</span>
                        <span class="inline-flex items-center gap-2 font-mono text-[0.65rem] uppercase tracking-[0.12em] text-emerald-300">
                            <span class="size-1.5 rounded-full bg-emerald-400" aria-hidden="true"></span> Disponible
                        </span>
                    </div>

                    <div class="service-panel__intro">
                        <span class="font-mono text-[0.64rem] font-semibold uppercase tracking-[0.18em] text-signal-300">Del problema al producto</span>
                        <h2 id="hero-services-title" class="mt-4 max-w-xl text-balance text-2xl font-semibold leading-tight tracking-[-0.035em] text-white sm:text-3xl">
                            Qué puedo construir contigo.
                        </h2>
                        <p class="mt-3 max-w-xl text-pretty text-sm leading-7 text-white/55">
                            Software pensado para ordenar la operación diaria y seguir siendo mantenible cuando el proyecto crece.
                        </p>
                    </div>

                    <ol class="service-panel__services">
                        @foreach ($services as $service)
                            <li class="service-panel__service" data-service="{{ Str::slug($service['title']) }}">
                                <div class="flex items-center justify-between gap-4">
                                    <span class="service-panel__index">0{{ $loop->iteration }}</span>
                                    <span class="font-mono text-[0.58rem] font-semibold uppercase tracking-[0.16em] text-white/30">{{ $service['label'] }}</span>
                                </div>
                                <div class="service-panel__copy mt-4">
                                    <h3 class="text-base font-semibold leading-snug text-white">{{ $service['title'] }}</h3>
                                    <p class="mt-2 text-pretty text-xs leading-5 text-white/50">{{ $service['description'] }}</p>
                                </div>
                            </li>
                        @endforeach
                    </ol>

                    <div class="service-panel__process">
                        <div class="flex flex-wrap items-center gap-x-2 gap-y-1 font-mono text-[0.56rem] font-semibold uppercase tracking-[0.12em] text-white/35" aria-label="Proceso de trabajo">
                            @foreach ($deliverySteps as $step)
                                <span>{{ $step }}</span>
                                @unless ($loop->last)
                                    <span class="text-signal-400/60" aria-hidden="true">→</span>
                                @endunless
                            @endforeach
                        </div>
                        <a href="#contacto" class="inline-flex min-h-11 shrink-0 items-center gap-2 rounded-xl text-sm font-semibold text-white/80 transition-colors hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-400 focus-visible:ring-offset-4 focus-visible:ring-offset-ink-950">
                            Hablemos <span aria-hidden="true">↗</span>
                        </a>
                    </div>
                </section>
                <div class="absolute -bottom-7 -left-7 -z-10 size-32 rounded-full bg-signal-400/18 blur-3xl" aria-hidden="true"></div>
            </div>
        </div>
    </section>

    <section id="sobre-mi" class="portfolio-section border-y border-ink-950/8 bg-white/55 py-20 dark:border-white/10 dark:bg-white/[.025] sm:py-28 lg:py-32">
        <div class="portfolio-container grid gap-12 lg:grid-cols-[.72fr_1.28fr] lg:gap-20">
            <x-portfolio.section-heading index="01" eyebrow="Perfil" title="Código con contexto, no solo entregables." />

            <div class="lg:pt-10" data-reveal>
                <p class="max-w-3xl text-balance text-2xl font-medium leading-[1.35] tracking-[-0.025em] text-ink-900 dark:text-slate-100 sm:text-3xl">
                    {{ config('portfolio.about') }}
                </p>
                <div class="mt-10 grid gap-4 sm:grid-cols-3">
                    <div class="portfolio-note">
                        <span class="portfolio-note__index">01</span>
                        <p>Arquitectura mantenible y decisiones técnicas claras.</p>
                    </div>
                    <div class="portfolio-note">
                        <span class="portfolio-note__index">02</span>
                        <p>Interfaces útiles para procesos reales y personas reales.</p>
                    </div>
                    <div class="portfolio-note">
                        <span class="portfolio-note__index">03</span>
                        <p>Calidad respaldada por validación y pruebas automatizadas.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section id="proyectos" class="portfolio-section py-20 sm:py-28 lg:py-32">
        <div class="portfolio-container">
            <div class="flex flex-col gap-6 sm:flex-row sm:items-end sm:justify-between">
                <x-portfolio.section-heading index="02" eyebrow="Trabajo seleccionado" title="Sistemas construidos para resolver." description="Una selección de productos reales, sus decisiones técnicas y el problema que atienden." class="mb-0" />
                <a href="{{ route('portfolio.projects.index') }}" class="portfolio-text-link mb-1 shrink-0">Ver todos los proyectos <span aria-hidden="true">↗</span></a>
            </div>
        </div>

        <div class="mx-auto mt-12 w-full max-w-[82rem] px-4">
            @if ($featuredProjects->isNotEmpty())
                <x-portfolio.project-carousel :projects="$featuredProjects" />
            @else
                <div class="portfolio-empty">
                    <p class="font-semibold text-ink-900 dark:text-slate-100">Aún no hay proyectos destacados.</p>
                    <p class="mt-2 text-sm text-ink-500 dark:text-slate-400">Los proyectos publicados y marcados como destacados aparecerán aquí automáticamente.</p>
                </div>
            @endif
        </div>
    </section>

    <section id="habilidades" class="portfolio-section border-y border-ink-950/8 bg-paper-100 py-20 dark:border-white/10 dark:bg-[#0a1525] sm:py-28 lg:py-32">
        <div class="portfolio-container">
            <x-portfolio.section-heading index="03" eyebrow="Herramientas" title="Tecnologías con las que construyo." description="Agrupadas por el papel que cumplen en mis proyectos y conectadas con trabajo real." />

            <div class="grid gap-6 lg:grid-cols-2">
                @foreach ($skills as $group => $groupSkills)
                    <section class="portfolio-skill-group" data-reveal aria-labelledby="skill-group-{{ Str::slug($group) }}">
                        <div class="mb-5 flex items-center justify-between gap-4">
                            <h3 id="skill-group-{{ Str::slug($group) }}" class="text-lg font-semibold tracking-tight text-ink-950 dark:text-white">{{ $group }}</h3>
                            <span class="font-mono text-xs text-ink-400 dark:text-slate-500">{{ str_pad((string) $groupSkills->count(), 2, '0', STR_PAD_LEFT) }}</span>
                        </div>
                        <div class="grid gap-2 sm:grid-cols-2">
                            @foreach ($groupSkills as $skill)
                                <x-portfolio.skill-chip :skill="$skill" />
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
        </div>
    </section>

    @if ($experiences->isNotEmpty())
        <section id="experiencia" class="portfolio-section py-20 sm:py-28 lg:py-32">
            <div class="portfolio-container grid gap-12 lg:grid-cols-[.72fr_1.28fr] lg:gap-20">
                <x-portfolio.section-heading index="04" eyebrow="Trayectoria" title="Experiencia profesional." />
                <div class="lg:pt-10" data-reveal>
                    @foreach ($experiences as $experience)
                        <x-portfolio.experience-item :experience="$experience" :index="$loop->iteration" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="contacto" class="portfolio-section bg-ink-950 py-20 text-white dark:bg-[#040b16] sm:py-28 lg:py-32">
        <div class="portfolio-container grid gap-14 lg:grid-cols-[.82fr_1.18fr] lg:gap-24">
            <div>
                <div class="mb-5 flex items-center gap-3">
                    <span class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-signal-300">{{ $experiences->isNotEmpty() ? '05' : '04' }}</span>
                    <span class="h-px w-8 bg-signal-400" aria-hidden="true"></span>
                    <span class="font-mono text-[0.68rem] uppercase tracking-[0.2em] text-white/45">Contacto</span>
                </div>
                <h2 class="max-w-xl text-balance text-4xl font-semibold tracking-[-0.045em] sm:text-5xl lg:text-6xl">Convirtamos una necesidad en un producto útil.</h2>
                <p class="mt-6 max-w-xl text-pretty text-base leading-8 text-white/60">{{ config('portfolio.availability') }} {{ config('portfolio.response_time') }}</p>

                <div class="mt-10 space-y-3">
                    <a href="mailto:{{ config('portfolio.email') }}" class="inline-flex min-h-11 items-center gap-3 text-sm font-medium text-white/75 transition-colors hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-400">
                        <span class="grid size-9 place-items-center rounded-xl border border-white/12" aria-hidden="true">@</span>
                        {{ config('portfolio.email') }}
                    </a>
                    <div class="flex flex-wrap gap-2 pt-2">
                        @foreach (config('portfolio.socials') as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Abrir {{ $social['name'] }} en una pestaña nueva" class="grid size-11 place-items-center rounded-xl border border-white/12 text-white/55 transition-[color,border-color,transform] hover:-translate-y-0.5 hover:border-white/25 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-400">
                                <x-portfolio.social-icon :name="$social['icon']" class="size-4" />
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="text-ink-950 dark:text-slate-100" data-reveal>
                <livewire:portfolio.contact-form />
            </div>
        </div>
    </section>
@endsection
