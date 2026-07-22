@extends('layouts.portfolio', [
    'title' => $project->title,
    'description' => $metaDescription,
    'canonical' => $canonicalUrl,
    'image' => $metaImage,
    'type' => 'article',
    'schema' => $schema,
])

@section('content')
    <div class="min-h-screen bg-slate-50 text-slate-950">
        <div class="mx-auto max-w-6xl px-4 py-8 sm:px-6 sm:py-12 lg:px-8">
            <nav class="mb-10" aria-label="Ruta de navegación">
                <ol class="flex flex-wrap items-center gap-2 font-mono text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-slate-400">
                    <li>
                        <a href="{{ route('home') }}" class="transition-colors hover:text-sky-700 focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500">Inicio</a>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li>
                        <a href="{{ route('portfolio.projects.index') }}" class="transition-colors hover:text-sky-700 focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500">Proyectos</a>
                    </li>
                    <li aria-hidden="true">/</li>
                    <li class="max-w-48 truncate text-slate-700" aria-current="page">{{ $project->title }}</li>
                </ol>
            </nav>

            <article>
                <header class="grid gap-10 lg:grid-cols-[minmax(0,1fr)_17rem] lg:items-end">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 font-mono text-[0.68rem] font-semibold uppercase tracking-[0.16em]">
                            @if ($project->category)
                                <span class="rounded-full bg-sky-50 px-3 py-1.5 text-sky-700 ring-1 ring-inset ring-sky-100">
                                    {{ $project->category->name }}
                                </span>
                            @endif

                            @if ($project->isInProgress())
                                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-emerald-700 ring-1 ring-inset ring-emerald-100">
                                    <span class="size-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                    En curso
                                </span>
                            @endif
                        </div>

                        <h1 class="mt-6 max-w-4xl text-4xl font-semibold tracking-[-0.045em] text-balance sm:text-5xl lg:text-6xl">
                            {{ $project->title }}
                        </h1>

                        <p class="mt-6 max-w-3xl text-lg leading-8 text-slate-600 sm:text-xl sm:leading-9">
                            {{ $project->summary }}
                        </p>
                    </div>

                    <dl class="border-l-2 border-sky-500 pl-5 text-sm">
                        <div>
                            <dt class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-slate-400">Proyecto</dt>
                            <dd class="mt-1 font-semibold text-slate-900">#{{ str_pad((string) $project->id, 2, '0', STR_PAD_LEFT) }}</dd>
                        </div>
                        @if ($project->started_at)
                            <div class="mt-5">
                                <dt class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-slate-400">Periodo</dt>
                                <dd class="mt-1 font-semibold text-slate-900">
                                    <time datetime="{{ $project->started_at->toDateString() }}">{{ $project->started_at->format('Y') }}</time>
                                    <span aria-hidden="true">—</span>
                                    @if ($project->finished_at)
                                        <time datetime="{{ $project->finished_at->toDateString() }}">{{ $project->finished_at->format('Y') }}</time>
                                    @else
                                        Presente
                                    @endif
                                </dd>
                            </div>
                        @endif
                    </dl>
                </header>

                @if ($project->demo_url || $project->repo_url)
                    <div class="mt-8 flex flex-wrap gap-3">
                        @if ($project->demo_url)
                            <a
                                href="{{ $project->demo_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex min-h-12 items-center gap-2 rounded-2xl bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
                            >
                                Abrir demostración <span aria-hidden="true">↗</span>
                                <span class="sr-only">en una pestaña nueva</span>
                            </a>
                        @endif
                        @if ($project->repo_url)
                            <a
                                href="{{ $project->repo_url }}"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="inline-flex min-h-12 items-center gap-2 rounded-2xl border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
                            >
                                Revisar código <span aria-hidden="true">↗</span>
                                <span class="sr-only">en una pestaña nueva</span>
                            </a>
                        @endif
                    </div>
                @endif

                <div class="mt-10 overflow-hidden rounded-3xl border border-slate-200 bg-slate-950 shadow-[0_28px_65px_-40px_rgba(15,23,42,0.75)] sm:mt-12">
                    @if ($coverUrl)
                        <img
                            src="{{ $coverUrl }}"
                            alt="Portada del proyecto {{ $project->title }}"
                            width="1600"
                            height="1000"
                            fetchpriority="high"
                            decoding="async"
                            class="aspect-[16/10] h-auto w-full object-cover"
                        >
                    @else
                        <div class="flex aspect-[16/8] flex-col justify-between p-7 text-white sm:p-10">
                            <p class="font-mono text-xs font-semibold uppercase tracking-[0.22em] text-sky-300">Case study / {{ str_pad((string) $project->id, 2, '0', STR_PAD_LEFT) }}</p>
                            <div>
                                <span class="mb-4 block h-px w-16 bg-sky-400"></span>
                                <p class="max-w-2xl text-2xl font-semibold tracking-[-0.03em] sm:text-4xl">{{ $project->title }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="mt-14 grid gap-12 lg:grid-cols-[minmax(0,1fr)_18rem] lg:gap-16 sm:mt-16">
                    <section aria-labelledby="project-story-title">
                        <p class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-sky-700">Contexto / solución</p>
                        <h2 id="project-story-title" class="mt-2 text-2xl font-semibold tracking-[-0.025em] text-slate-950 sm:text-3xl">
                            Sobre el proyecto
                        </h2>

                        @if ($project->description)
                            <div class="mt-6 space-y-5 text-base leading-8 text-slate-700">
                                @foreach (preg_split('/\R{2,}/', trim($project->description)) as $paragraph)
                                    <p>{{ trim($paragraph) }}</p>
                                @endforeach
                            </div>
                        @else
                            <p class="mt-6 text-base leading-8 text-slate-600">{{ $project->summary }}</p>
                        @endif
                    </section>

                    @if ($project->skills->isNotEmpty())
                        <aside aria-labelledby="project-stack-title">
                            <h2 id="project-stack-title" class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-slate-400">
                                Stack técnico
                            </h2>
                            <ul class="mt-4 divide-y divide-slate-200 border-y border-slate-200">
                                @foreach ($project->skills as $skill)
                                    <li class="flex items-center justify-between gap-3 py-3 text-sm">
                                        <span class="font-medium text-slate-800">{{ $skill->name }}</span>
                                        @if ($skill->group)
                                            <span class="font-mono text-[0.65rem] uppercase tracking-wider text-slate-400">{{ $skill->group }}</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </aside>
                    @endif
                </div>

                <x-portfolio.project-gallery :project="$project" />
            </article>

            <div class="mt-16 border-t border-slate-200 pt-8 sm:mt-20">
                <a
                    href="{{ route('portfolio.projects.index') }}"
                    class="inline-flex min-h-11 items-center gap-2 rounded-xl text-sm font-semibold text-slate-700 transition-colors hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
                >
                    <span aria-hidden="true">←</span> Volver al archivo de proyectos
                </a>
            </div>
        </div>
    </div>
@endsection
