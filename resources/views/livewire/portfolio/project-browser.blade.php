<div class="min-h-screen bg-slate-50 text-slate-950">
    <section class="border-b border-slate-200 bg-white" aria-labelledby="projects-page-title">
        <div class="mx-auto max-w-7xl px-4 py-16 sm:px-6 sm:py-20 lg:px-8 lg:py-24">
            <p class="font-mono text-xs font-semibold uppercase tracking-[0.22em] text-sky-700">
                Proyectos / archivo público
            </p>
            <div class="mt-5 grid gap-8 lg:grid-cols-[minmax(0,1fr)_22rem] lg:items-end">
                <div>
                    <h1 id="projects-page-title" class="max-w-4xl text-4xl font-semibold tracking-[-0.04em] text-balance sm:text-5xl lg:text-6xl">
                        Sistemas construidos para resolver problemas reales.
                    </h1>
                    <p class="mt-6 max-w-2xl text-base leading-8 text-slate-600 sm:text-lg">
                        Una selección de productos web centrados en gestión, trazabilidad y experiencias de uso claras.
                    </p>
                </div>
                <div class="border-l-2 border-sky-500 pl-5">
                    <p class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.18em] text-slate-400">Índice activo</p>
                    <p class="mt-2 text-3xl font-semibold tabular-nums text-slate-950">{{ str_pad((string) $projects->total(), 2, '0', STR_PAD_LEFT) }}</p>
                    <p class="mt-1 text-sm text-slate-500">{{ $projects->total() === 1 ? 'proyecto publicado' : 'proyectos publicados' }}</p>
                </div>
            </div>
        </div>
    </section>

    <section class="mx-auto max-w-7xl px-4 py-10 sm:px-6 sm:py-12 lg:px-8" aria-label="Explorar proyectos">
        <div class="rounded-3xl border border-slate-200 bg-white p-4 shadow-[0_18px_45px_-38px_rgba(15,23,42,0.4)] sm:p-5">
            <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-[minmax(16rem,1.45fr)_minmax(12rem,1fr)_minmax(12rem,1fr)_auto]">
                <div>
                    <label for="project-search" class="sr-only">Buscar proyectos</label>
                    <div class="relative">
                        <svg class="pointer-events-none absolute left-4 top-1/2 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                            <circle cx="11" cy="11" r="7"></circle>
                            <path d="m20 20-3.2-3.2"></path>
                        </svg>
                        <input
                            id="project-search"
                            type="search"
                            wire:model.live.debounce.350ms="search"
                            placeholder="Buscar por nombre o propósito"
                            maxlength="80"
                            autocomplete="off"
                            aria-controls="project-results"
                            class="min-h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 py-3 pl-11 pr-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                        >
                    </div>
                </div>

                <div>
                    <label for="project-category" class="sr-only">Filtrar por categoría</label>
                    <select
                        id="project-category"
                        wire:model.live="category"
                        aria-controls="project-results"
                        class="min-h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                    >
                        <option value="">Todas las categorías</option>
                        @foreach ($this->categories as $categoryOption)
                            <option value="{{ $categoryOption->slug }}">{{ $categoryOption->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="project-technology" class="sr-only">Filtrar por tecnología</label>
                    <select
                        id="project-technology"
                        wire:model.live="technology"
                        aria-controls="project-results"
                        class="min-h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-700 outline-none transition hover:border-slate-300 focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                    >
                        <option value="">Todas las tecnologías</option>
                        @foreach ($this->technologies->groupBy(fn ($skill) => $skill->group ?: 'Otras') as $group => $skills)
                            <optgroup label="{{ $group }}">
                                @foreach ($skills as $skillOption)
                                    <option value="{{ $skillOption->slug }}">{{ $skillOption->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                </div>

                @if (filled($search) || filled($category) || filled($technology))
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="inline-flex min-h-12 items-center justify-center rounded-2xl border border-slate-200 px-4 text-sm font-semibold text-slate-600 transition hover:border-sky-200 hover:bg-sky-50 hover:text-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
                    >
                        Limpiar
                    </button>
                @endif
            </div>
        </div>

        <div class="mt-8 flex flex-wrap items-center justify-between gap-3">
            <p class="text-sm text-slate-500" aria-live="polite">
                Mostrando
                <span class="font-semibold tabular-nums text-slate-900">{{ $projects->firstItem() ?? 0 }}–{{ $projects->lastItem() ?? 0 }}</span>
                de
                <span class="font-semibold tabular-nums text-slate-900">{{ $projects->total() }}</span>
            </p>
            <div
                wire:loading.flex
                wire:target="search,category,technology,clearFilters,gotoPage,previousPage,nextPage"
                class="items-center gap-2 font-mono text-[0.68rem] font-semibold uppercase tracking-[0.16em] text-sky-700"
                role="status"
            >
                <span class="size-2 animate-pulse rounded-full bg-sky-500" aria-hidden="true"></span>
                Actualizando archivo
            </div>
        </div>

        <div
            id="project-results"
            class="mt-6"
            wire:loading.class="opacity-45"
            wire:target="search,category,technology,clearFilters,gotoPage,previousPage,nextPage"
        >
            @if ($projects->isEmpty())
                <div class="rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-16 text-center sm:py-20">
                    <span class="mx-auto flex size-12 items-center justify-center rounded-2xl bg-slate-950 font-mono text-sm font-semibold text-sky-300" aria-hidden="true">00</span>
                    <h2 class="mt-5 text-xl font-semibold text-slate-950">No encontramos coincidencias</h2>
                    <p class="mx-auto mt-2 max-w-md text-sm leading-7 text-slate-500">
                        Ajusta la búsqueda o elimina los filtros para volver a consultar todos los proyectos publicados.
                    </p>
                    <button
                        type="button"
                        wire:click="clearFilters"
                        class="mt-6 inline-flex min-h-11 items-center rounded-xl bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-sky-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
                    >
                        Ver todos los proyectos
                    </button>
                </div>
            @else
                <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-3">
                    @foreach ($projects as $project)
                        <x-portfolio.project-card :project="$project" wire:key="project-{{ $project->id }}" />
                    @endforeach
                </div>

                @if ($projects->hasPages())
                    <nav class="mt-10 border-t border-slate-200 pt-8" aria-label="Paginación de proyectos">
                        {{ $projects->onEachSide(1)->links() }}
                    </nav>
                @endif
            @endif
        </div>
    </section>
</div>
