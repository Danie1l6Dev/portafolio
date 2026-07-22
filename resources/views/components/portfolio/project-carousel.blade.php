@props(['projects'])

@php
    $projectCount = $projects->count();
@endphp

<div
    x-data="featuredProjectCarousel({{ $projectCount }})"
    x-bind:data-carousel-ready="ready ? 'true' : 'false'"
    x-on:keydown.left.prevent="handleArrowNavigation(-1, $event)"
    x-on:keydown.right.prevent="handleArrowNavigation(1, $event)"
    class="featured-project-carousel relative w-full select-none"
    role="region"
    aria-roledescription="carrusel"
    aria-labelledby="featured-projects-carousel-title"
    data-featured-carousel
>
    <h3 id="featured-projects-carousel-title" class="sr-only">Proyectos destacados</h3>

    <div
        id="featured-projects-track"
        class="featured-project-carousel__track"
        x-bind:style="ready ? { height: `${layout.trackHeight}px` } : {}"
        x-on:touchstart.passive="startSwipe($event)"
        x-on:touchend.passive="endSwipe($event)"
        aria-label="Selección de proyectos destacados"
    >
        @foreach ($projects as $project)
            @php
                $coverPath = $project->cover_image ? ltrim($project->cover_image, '/') : null;
                $coverUrl = match (true) {
                    blank($coverPath) => null,
                    str_starts_with($coverPath, 'http://'), str_starts_with($coverPath, 'https://') => $coverPath,
                    str_starts_with($coverPath, 'storage/') => asset($coverPath),
                    default => Illuminate\Support\Facades\Storage::disk('public')->url($coverPath),
                };
                $detailUrl = route('portfolio.projects.show', ['project' => $project->slug]);
                $slideIndex = $loop->index;
            @endphp

            <div
                class="featured-project-carousel__slide"
                x-bind:hidden="ready && ! isVisible({{ $slideIndex }})"
                x-bind:style="slideStyle({{ $slideIndex }})"
                x-bind:data-active="isActive({{ $slideIndex }}) ? 'true' : 'false'"
                x-bind:aria-hidden="ready && ! isActive({{ $slideIndex }}) ? 'true' : 'false'"
                role="group"
                aria-roledescription="diapositiva"
                aria-label="Proyecto {{ $loop->iteration }} de {{ $projectCount }}: {{ $project->title }}"
                data-featured-project="{{ $project->slug }}"
            >
                <a
                    href="{{ $detailUrl }}"
                    class="featured-project-carousel__link group"
                    x-bind:tabindex="isActive({{ $slideIndex }}) ? 0 : -1"
                    x-on:click="handleSlideClick($event, {{ $slideIndex }})"
                    x-on:keydown.space.prevent="handleSlideAction($event, {{ $slideIndex }})"
                    aria-label="Ver detalle del proyecto {{ $project->title }}"
                >
                    <article class="featured-project-carousel__card">
                        <div class="featured-project-carousel__media">
                            @if ($coverUrl)
                                <img
                                    src="{{ $coverUrl }}"
                                    alt="Portada del proyecto {{ $project->title }}"
                                    width="1280"
                                    height="720"
                                    loading="lazy"
                                    decoding="async"
                                    class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.025]"
                                >
                            @else
                                <div class="featured-project-carousel__placeholder">
                                    <span class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.22em] text-signal-300">
                                        Case / {{ str_pad((string) $project->id, 2, '0', STR_PAD_LEFT) }}
                                    </span>
                                    <div>
                                        <span class="mb-4 block h-px w-14 bg-signal-400"></span>
                                        <p class="max-w-md text-2xl font-semibold leading-tight text-white sm:text-3xl">{{ $project->title }}</p>
                                    </div>
                                </div>
                            @endif

                            <div class="featured-project-carousel__media-shade" aria-hidden="true"></div>

                            <div class="absolute left-4 top-4 flex flex-wrap gap-2 sm:left-5 sm:top-5">
                                <span class="inline-flex items-center gap-2 rounded-full bg-white/92 px-3 py-1.5 font-mono text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-ink-800 shadow-sm backdrop-blur">
                                    <span class="size-1.5 rounded-full bg-signal-500" aria-hidden="true"></span>
                                    Destacado
                                </span>

                                @if ($project->isInProgress())
                                    <span class="inline-flex items-center gap-2 rounded-full bg-emerald-50/95 px-3 py-1.5 font-mono text-[0.64rem] font-semibold uppercase tracking-[0.14em] text-emerald-700 shadow-sm backdrop-blur">
                                        <span class="size-1.5 rounded-full bg-emerald-500" aria-hidden="true"></span>
                                        En curso
                                    </span>
                                @endif
                            </div>

                            @if ($coverUrl)
                                <span class="featured-project-carousel__image-cta">
                                    Ver proyecto <span aria-hidden="true">↗</span>
                                </span>
                            @endif
                        </div>

                        <div class="featured-project-carousel__body">
                            <div class="flex flex-wrap items-center justify-between gap-3">
                                @if ($project->category)
                                    <span class="rounded-full bg-signal-50 px-3 py-1.5 font-mono text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-signal-700 ring-1 ring-inset ring-signal-100">
                                        {{ $project->category->name }}
                                    </span>
                                @else
                                    <span class="font-mono text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-ink-400">Proyecto</span>
                                @endif

                                <span class="font-mono text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-ink-400">
                                    0{{ $loop->iteration }} / {{ str_pad((string) $projectCount, 2, '0', STR_PAD_LEFT) }}
                                </span>
                            </div>

                            <h4 class="mt-4 line-clamp-2 text-2xl font-semibold tracking-[-0.035em] text-ink-950 sm:text-[1.75rem]">
                                {{ $project->title }}
                            </h4>

                            <p class="mt-3 line-clamp-3 text-sm leading-7 text-ink-600 sm:text-[0.95rem]">
                                {{ $project->summary }}
                            </p>

                            @if ($project->skills->isNotEmpty())
                                <ul class="featured-project-carousel__skills mt-5 flex flex-wrap gap-2" aria-label="Habilidades destacadas del proyecto">
                                    @foreach ($project->skills->take(5) as $skill)
                                        <li class="inline-flex items-center gap-1.5 rounded-lg bg-paper-100 px-2 py-1 font-mono text-[0.68rem] font-medium text-ink-600 ring-1 ring-inset ring-ink-950/5" data-carousel-skill="{{ $skill->slug }}">
                                            <x-portfolio.skill-icon :icon="$skill->icon" :name="$skill->name" size="sm" />
                                            <span>{{ $skill->name }}</span>
                                        </li>
                                    @endforeach

                                    @if ($project->skills->count() > 5)
                                        <li class="rounded-lg bg-paper-50 px-2.5 py-1.5 font-mono text-[0.68rem] font-medium text-ink-400 ring-1 ring-inset ring-ink-950/5">
                                            +{{ $project->skills->count() - 5 }}
                                            <span class="sr-only">habilidades más</span>
                                        </li>
                                    @endif
                                </ul>
                            @endif
                        </div>
                    </article>
                </a>
            </div>
        @endforeach
    </div>

    @if ($projectCount > 1)
        <button
            type="button"
            x-cloak
            x-on:click="previous()"
            x-bind:style="{ top: `${layout.navTop}px` }"
            class="featured-project-carousel__control featured-project-carousel__control--previous"
            aria-label="Proyecto anterior"
            aria-controls="featured-projects-track"
            data-carousel-control="previous"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" class="size-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m15 18-6-6 6-6" />
            </svg>
        </button>

        <button
            type="button"
            x-cloak
            x-on:click="next()"
            x-bind:style="{ top: `${layout.navTop}px` }"
            class="featured-project-carousel__control featured-project-carousel__control--next"
            aria-label="Siguiente proyecto"
            aria-controls="featured-projects-track"
            data-carousel-control="next"
        >
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" class="size-4" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="m9 6 6 6-6 6" />
            </svg>
        </button>
    @endif

    <div class="mt-4 flex min-h-8 items-center justify-center gap-2" x-cloak>
        @if ($projectCount <= 12)
            @foreach ($projects as $project)
                <button
                    type="button"
                    x-on:click="goTo({{ $loop->index }})"
                    x-bind:data-active="isActive({{ $loop->index }}) ? 'true' : 'false'"
                    x-bind:aria-current="isActive({{ $loop->index }}) ? 'true' : null"
                    class="featured-project-carousel__dot"
                    aria-label="Mostrar {{ $project->title }}"
                ></button>
            @endforeach
        @else
            <span class="font-mono text-xs tabular-nums text-ink-400" x-text="`${activeIndex + 1} / ${total}`"></span>
        @endif
    </div>

    <p
        class="sr-only"
        aria-live="polite"
        aria-atomic="true"
        data-carousel-status
        x-text="`Proyecto ${activeIndex + 1} de ${total}`"
    ></p>
</div>
