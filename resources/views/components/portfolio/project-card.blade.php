@props(['project'])

@php
    $coverPath = $project->cover_image ? ltrim($project->cover_image, '/') : null;
    $coverUrl = match (true) {
        blank($coverPath) => null,
        str_starts_with($coverPath, 'http://'), str_starts_with($coverPath, 'https://') => $coverPath,
        str_starts_with($coverPath, 'storage/') => asset($coverPath),
        default => Illuminate\Support\Facades\Storage::disk('public')->url($coverPath),
    };
    $detailUrl = route('portfolio.projects.show', ['project' => $project->slug]);
    $startedYear = $project->started_at?->format('Y');
    $finishedYear = $project->finished_at?->format('Y');
@endphp

<article {{ $attributes->class('group flex h-full flex-col overflow-hidden rounded-3xl border border-slate-200/90 bg-white shadow-[0_18px_45px_-34px_rgba(15,23,42,0.45)] transition duration-200 hover:-translate-y-1 hover:border-sky-200 hover:shadow-[0_24px_55px_-32px_rgba(2,132,199,0.35)] dark:border-white/10 dark:bg-[#0d1a2b] dark:shadow-[0_22px_60px_-34px_rgba(0,0,0,.85)] dark:hover:border-sky-400/40') }}>
    <a
        href="{{ $detailUrl }}"
        class="relative block aspect-[16/10] overflow-hidden bg-slate-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500 dark:bg-[#081321]"
        aria-label="Ver el proyecto {{ $project->title }}"
    >
        @if ($coverUrl)
            <img
                src="{{ $coverUrl }}"
                alt="Portada del proyecto {{ $project->title }}"
                width="1280"
                height="800"
                loading="lazy"
                decoding="async"
                class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.025]"
            >
        @else
            <div class="flex h-full flex-col justify-between bg-slate-950 p-6 text-slate-100">
                <span class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-sky-300">
                    Case / {{ str_pad((string) $project->id, 2, '0', STR_PAD_LEFT) }}
                </span>
                <div>
                    <span class="mb-3 block h-px w-12 bg-sky-400"></span>
                    <p class="max-w-xs text-xl font-semibold leading-tight">{{ $project->title }}</p>
                </div>
            </div>
        @endif

        <span class="absolute inset-x-0 bottom-0 h-px bg-sky-400/70 opacity-0 transition-opacity group-hover:opacity-100"></span>
    </a>

    <div class="flex flex-1 flex-col p-6 sm:p-7">
        <div class="mb-4 flex flex-wrap items-center gap-2 font-mono text-[0.68rem] font-semibold uppercase tracking-[0.16em]">
            @if ($project->category)
                <span class="rounded-full bg-sky-50 px-3 py-1.5 text-sky-700 ring-1 ring-inset ring-sky-100 dark:bg-sky-400/10 dark:text-sky-300 dark:ring-sky-400/20">
                    {{ $project->category->name }}
                </span>
            @endif

            @if ($project->isInProgress())
                <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-emerald-700 ring-1 ring-inset ring-emerald-100 dark:bg-emerald-400/10 dark:text-emerald-300 dark:ring-emerald-400/20">
                    <span class="size-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                    En curso
                </span>
            @elseif ($startedYear)
                <span class="px-1 text-slate-400 dark:text-slate-500">
                    {{ $startedYear }}{{ $finishedYear && $finishedYear !== $startedYear ? '—'.$finishedYear : '' }}
                </span>
            @endif
        </div>

        <h2 class="text-xl font-semibold tracking-[-0.025em] text-slate-950 dark:text-white sm:text-2xl">
            <a href="{{ $detailUrl }}" class="transition-colors hover:text-sky-700 focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 dark:hover:text-sky-300">
                {{ $project->title }}
            </a>
        </h2>

        <p class="mt-3 flex-1 text-sm leading-7 text-slate-600 dark:text-slate-300 sm:text-[0.95rem]">
            {{ $project->summary }}
        </p>

        @if ($project->skills->isNotEmpty())
            <ul class="mt-5 flex flex-wrap gap-2" aria-label="Tecnologías utilizadas">
                @foreach ($project->skills->take(4) as $skill)
                    <li class="rounded-lg bg-slate-100 px-2.5 py-1.5 font-mono text-[0.68rem] font-medium text-slate-600 dark:bg-white/[.07] dark:text-slate-300">
                        {{ $skill->name }}
                    </li>
                @endforeach

                @if ($project->skills->count() > 4)
                    <li class="rounded-lg bg-slate-50 px-2.5 py-1.5 font-mono text-[0.68rem] font-medium text-slate-400 dark:bg-white/[.04] dark:text-slate-500">
                        +{{ $project->skills->count() - 4 }}
                        <span class="sr-only">tecnologías más</span>
                    </li>
                @endif
            </ul>
        @endif

        <div class="mt-6 flex flex-wrap items-center justify-between gap-3 border-t border-slate-100 pt-5 dark:border-white/10">
            <a
                href="{{ $detailUrl }}"
                class="inline-flex min-h-10 items-center gap-2 text-sm font-semibold text-slate-900 transition-colors hover:text-sky-700 focus-visible:rounded focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 dark:text-slate-100 dark:hover:text-sky-300"
            >
                Ver caso
                <span aria-hidden="true">↗</span>
            </a>

            <div class="flex items-center gap-2">
                @if ($project->demo_url)
                    <a
                        href="{{ $project->demo_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex min-h-10 items-center rounded-xl px-3 text-xs font-semibold text-slate-500 transition-colors hover:bg-sky-50 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 dark:text-slate-400 dark:hover:bg-white/[.07] dark:hover:text-sky-300"
                        aria-label="Abrir demo de {{ $project->title }} en una pestaña nueva"
                    >
                        Demo
                    </a>
                @endif

                @if ($project->repo_url)
                    <a
                        href="{{ $project->repo_url }}"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="inline-flex min-h-10 items-center rounded-xl px-3 text-xs font-semibold text-slate-500 transition-colors hover:bg-sky-50 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 dark:text-slate-400 dark:hover:bg-white/[.07] dark:hover:text-sky-300"
                        aria-label="Abrir repositorio de {{ $project->title }} en una pestaña nueva"
                    >
                        Código
                    </a>
                @endif
            </div>
        </div>
    </div>
</article>
