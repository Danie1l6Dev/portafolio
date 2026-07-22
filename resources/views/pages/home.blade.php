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

    <section id="sobre-mi" class="portfolio-section border-y border-ink-950/8 bg-[#f4f7fa] py-20 dark:border-white/10 dark:bg-[#0a1728] sm:py-28 lg:py-32">
        <div class="portfolio-container">
            <div class="grid gap-8 lg:grid-cols-[minmax(0,.78fr)_minmax(22rem,.42fr)] lg:items-start lg:gap-16">
                <x-portfolio.section-heading
                    index="01"
                    eyebrow="Perfil"
                    title="Código con contexto, no solo entregables."
                    description="Conecto la necesidad operativa con la arquitectura, la interfaz y la calidad del producto."
                    class="mb-0"
                />

                <aside class="education-mark lg:mb-1" aria-label="Formación académica">
                    <div class="education-mark__logo-shell" aria-hidden="true">
                        <img src="{{ asset('images/education/uniguajira-logo.webp') }}" alt="" class="education-mark__logo">
                    </div>
                    <div class="min-w-0">
                    <span class="font-mono text-[0.64rem] font-semibold uppercase tracking-[0.18em] text-signal-700 dark:text-signal-300">Formación actual</span>
                    <h3 class="mt-2 text-lg font-semibold tracking-tight text-ink-950 dark:text-white">{{ config('portfolio.education.program') }}</h3>
                    <p class="mt-1 text-sm leading-6 text-ink-600 dark:text-slate-300">{{ config('portfolio.education.institution') }}</p>
                    <p class="mt-2 font-mono text-[0.68rem] uppercase tracking-[0.12em] text-ink-400 dark:text-slate-500">{{ config('portfolio.education.period') }} · {{ config('portfolio.education.location') }}</p>
                    </div>
                </aside>
            </div>

            <div class="profile-panel mt-12 lg:mt-16" data-reveal>
                <div class="profile-panel__intro">
                    <div class="profile-panel__intro-top">
                        <div>
                        <span class="profile-panel__label">Enfoque profesional</span>
                        <p class="mt-5 max-w-none text-balance text-2xl font-medium leading-[1.35] tracking-[-0.03em] text-ink-950 dark:text-white sm:text-3xl">
                            {{ config('portfolio.about') }}
                        </p>
                        </div>

                        <figure class="profile-portrait">
                            <img src="{{ asset('images/profile/daniel-sierra.webp') }}" alt="Daniel Sierra, desarrollador de software" class="profile-portrait__image">
                            <figcaption class="profile-portrait__tag">Daniel Sierra <span aria-hidden="true">·</span> Software</figcaption>
                        </figure>
                    </div>

                    <div class="mt-10 border-t border-ink-950/8 pt-6 dark:border-white/10">
                        <div class="flex flex-col gap-6 xl:flex-row xl:items-end xl:justify-between">
                            <div>
                                <p class="font-mono text-[0.64rem] font-semibold uppercase tracking-[0.18em] text-ink-400 dark:text-slate-500">De la necesidad al producto</p>
                                <div class="mt-4 flex flex-wrap items-center gap-x-3 gap-y-2 font-mono text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-ink-600 dark:text-slate-300" aria-label="Proceso de trabajo">
                                    <span>Entender</span>
                                    <span class="text-signal-600 dark:text-signal-400" aria-hidden="true">→</span>
                                    <span>Diseñar</span>
                                    <span class="text-signal-600 dark:text-signal-400" aria-hidden="true">→</span>
                                    <span>Construir</span>
                                    <span class="text-signal-600 dark:text-signal-400" aria-hidden="true">→</span>
                                    <span>Validar</span>
                                </div>
                            </div>

                            <a
                                href="{{ asset(config('portfolio.resume.path')) }}"
                                download="{{ config('portfolio.resume.download_name') }}"
                                type="application/pdf"
                                class="portfolio-button portfolio-button--secondary portfolio-button--small shrink-0"
                                data-resume-download
                            >
                                Descargar hoja de vida
                                <svg aria-hidden="true" class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v12m0 0 4-4m-4 4-4-4M5 20h14" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>

                <figure class="profile-portrait profile-portrait--feature" aria-label="Daniel Sierra">
                    <img src="{{ asset('images/profile/daniel-sierra.webp') }}" alt="Daniel Sierra, desarrollador de software" class="profile-portrait__image">
                    <figcaption class="profile-portrait__tag">Daniel Sierra <span aria-hidden="true">·</span> Software</figcaption>
                </figure>

                <ol class="profile-principles" aria-label="Principios de trabajo">
                    <li class="profile-principle">
                        <span class="profile-principle__index">01</span>
                        <div>
                            <h3 class="profile-principle__title">Arquitectura con intención</h3>
                            <p class="profile-principle__copy">Estructuras mantenibles y decisiones técnicas que siguen siendo claras cuando el proyecto crece.</p>
                        </div>
                    </li>
                    <li class="profile-principle">
                        <span class="profile-principle__index">02</span>
                        <div>
                            <h3 class="profile-principle__title">Experiencias útiles</h3>
                            <p class="profile-principle__copy">Interfaces pensadas alrededor de procesos reales, con menos fricción para las personas.</p>
                        </div>
                    </li>
                    <li class="profile-principle">
                        <span class="profile-principle__index">03</span>
                        <div>
                            <h3 class="profile-principle__title">Calidad verificable</h3>
                            <p class="profile-principle__copy">Validaciones y pruebas automatizadas que respaldan el comportamiento del producto.</p>
                        </div>
                    </li>
                </ol>
            </div>
        </div>
    </section>

    <section id="proyectos" class="portfolio-section portfolio-section--textured py-20 sm:py-28 lg:py-32">
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

    <section id="habilidades" class="portfolio-section border-y border-ink-950/8 bg-[#f4f7fa] py-20 dark:border-white/10 dark:bg-[#0a1728] sm:py-28 lg:py-32">
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
        <section id="experiencia" class="portfolio-section portfolio-section--textured bg-paper-50 py-20 dark:bg-[#07111f] sm:py-28 lg:py-32">
            <div class="portfolio-container grid gap-12 lg:grid-cols-[.72fr_1.28fr] lg:gap-20">
                <x-portfolio.section-heading index="04" eyebrow="Trayectoria" title="Experiencia y acompañamiento académico." description="Trabajo aplicado en desarrollo de software y formación en fundamentos de programación." />
                <div class="lg:pt-10" data-reveal>
                    @foreach ($experiences as $experience)
                        <x-portfolio.experience-item :experience="$experience" :index="$loop->iteration" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($achievements->isNotEmpty())
        <section id="logros" class="portfolio-section portfolio-section--textured border-t border-ink-950/8 bg-paper-50 py-20 dark:border-white/10 dark:bg-[#07111f] sm:py-28 lg:py-32">
            <div class="portfolio-container">
                <div class="grid gap-8 lg:grid-cols-[.72fr_1.28fr] lg:items-end lg:gap-20">
                    <x-portfolio.section-heading
                        :index="$achievementSectionIndex"
                        eyebrow="Evidencia"
                        title="Logros que respaldan el trabajo."
                        description="Hackathons, certificaciones y reconocimientos acompañados por resultados, contexto y evidencia verificable."
                        class="mb-0"
                    />
                    <p class="max-w-2xl text-pretty text-base leading-8 text-ink-600 dark:text-slate-400 lg:pb-1">
                        Más que acumular diplomas, esta selección muestra situaciones en las que una idea, una colaboración o una habilidad produjo un resultado concreto.
                    </p>
                </div>

                <div class="mt-12 grid gap-6 lg:grid-cols-2 lg:gap-8">
                    @foreach ($achievements as $achievement)
                        <x-portfolio.achievement-card :achievement="$achievement" :index="$loop->iteration" />
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <section id="contacto" class="portfolio-section bg-[#f4f7fa] py-20 text-ink-950 dark:bg-[#0a1728] dark:text-white sm:py-28 lg:py-32">
        <div class="portfolio-container grid gap-14 lg:grid-cols-[.82fr_1.18fr] lg:gap-24">
            <div>
                <div class="mb-5 flex items-center gap-3">
                    <span class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-signal-300">{{ $contactSectionIndex }}</span>
                    <span class="h-px w-8 bg-signal-400" aria-hidden="true"></span>
                    <span class="font-mono text-[0.68rem] uppercase tracking-[0.2em] text-ink-500 dark:text-white/45">Contacto</span>
                </div>
                <h2 class="max-w-xl text-balance text-4xl font-semibold tracking-[-0.045em] sm:text-5xl lg:text-6xl">Convirtamos una necesidad en un producto útil.</h2>
                <p class="mt-6 max-w-xl text-pretty text-base leading-8 text-ink-600 dark:text-white/60">{{ config('portfolio.availability') }} {{ config('portfolio.response_time') }}</p>

                <div class="mt-10 space-y-3">
                    <a href="mailto:{{ config('portfolio.email') }}" class="inline-flex min-h-11 items-center gap-3 text-sm font-medium text-ink-700 transition-colors hover:text-ink-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-400 dark:text-white/75 dark:hover:text-white">
                        <span class="grid size-9 place-items-center rounded-xl border border-ink-950/12 dark:border-white/12" aria-hidden="true">@</span>
                        {{ config('portfolio.email') }}
                    </a>
                    <div class="flex flex-wrap gap-2 pt-2">
                        @foreach (config('portfolio.socials') as $social)
                            <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Abrir {{ $social['name'] }} en una pestaña nueva" class="grid size-11 place-items-center rounded-xl border border-ink-950/12 text-ink-500 transition-[color,border-color,transform] hover:-translate-y-0.5 hover:border-ink-950/25 hover:text-ink-950 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-400 dark:border-white/12 dark:text-white/55 dark:hover:border-white/25 dark:hover:text-white">
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
