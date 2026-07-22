@props(['achievement', 'index'])

<article class="achievement-card group" data-achievement="{{ $achievement->id }}" data-reveal>
    <div class="achievement-card__visual">
        @if ($achievement->imageUrl())
            <img
                src="{{ $achievement->imageUrl() }}"
                alt="Evidencia visual de {{ $achievement->title }}"
                class="h-full w-full object-cover transition-transform duration-500 group-hover:scale-[1.025]"
                loading="lazy"
            >
        @else
            <div class="achievement-card__placeholder" aria-hidden="true">
                <svg class="size-12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5h7.5v4.875a3.75 3.75 0 0 1-7.5 0V4.5Z" />
                    <path stroke-linecap="round" d="M8.25 6H5.5v1.25A3.25 3.25 0 0 0 8.75 10.5M15.75 6h2.75v1.25a3.25 3.25 0 0 1-3.25 3.25M12 13.125V17m-3 2.5h6" />
                </svg>
                <span class="font-mono text-[0.62rem] font-semibold uppercase tracking-[0.2em]">Evidencia verificable</span>
            </div>
        @endif

        <div class="absolute inset-x-0 top-0 flex items-center justify-between gap-3 p-4">
            <span class="rounded-full border border-white/15 bg-[#040b16]/85 px-3 py-1.5 font-mono text-[0.62rem] font-semibold uppercase tracking-[0.14em] text-white backdrop-blur">{{ $achievement->type->label() }}</span>
            <span class="rounded-full border border-white/15 bg-[#040b16]/85 px-3 py-1.5 font-mono text-[0.62rem] text-white/70 backdrop-blur">{{ str_pad((string) $index, 2, '0', STR_PAD_LEFT) }}</span>
        </div>
    </div>

    <div class="achievement-card__body">
        <div class="flex flex-wrap items-center gap-x-3 gap-y-2">
            <time datetime="{{ $achievement->achieved_at->toDateString() }}" class="font-mono text-[0.67rem] font-semibold uppercase tracking-[0.14em] text-signal-700 dark:text-signal-300">{{ $achievement->achieved_at->translatedFormat('F Y') }}</time>
            @if ($achievement->result)
                <span class="size-1 rounded-full bg-ink-300 dark:bg-white/25" aria-hidden="true"></span>
                <span class="text-xs font-semibold text-ink-600 dark:text-slate-300">{{ $achievement->result }}</span>
            @endif
        </div>

        <h3 class="mt-4 text-balance text-2xl font-semibold leading-tight tracking-[-0.035em] text-ink-950 dark:text-white">{{ $achievement->title }}</h3>
        <p class="mt-2 text-sm font-medium text-ink-600 dark:text-slate-300">{{ $achievement->organization }}</p>

        @if ($achievement->description)
            <p class="mt-5 text-pretty text-sm leading-7 text-ink-600 dark:text-slate-400">{{ $achievement->description }}</p>
        @endif

        @if ($achievement->role)
            <div class="mt-5 border-l-2 border-signal-500 pl-4">
                <span class="font-mono text-[0.6rem] font-semibold uppercase tracking-[0.16em] text-ink-400 dark:text-slate-500">Mi aporte</span>
                <p class="mt-1 text-sm font-medium text-ink-800 dark:text-slate-200">{{ $achievement->role }}</p>
            </div>
        @endif

        <x-portfolio.achievement-gallery :achievement="$achievement" />

        @if ($achievement->certificate_path || $achievement->external_url)
            <div class="mt-7 flex flex-wrap gap-3 border-t border-ink-950/8 pt-5 dark:border-white/10">
                @if ($achievement->certificate_path)
                    <a href="{{ $achievement->certificateUrl() }}" target="_blank" rel="noopener noreferrer" class="portfolio-text-link">Ver certificado <span aria-hidden="true">↗</span></a>
                @endif
                @if ($achievement->external_url)
                    <a href="{{ $achievement->external_url }}" target="_blank" rel="noopener noreferrer" class="portfolio-text-link">Ver evidencia <span aria-hidden="true">↗</span></a>
                @endif
            </div>
        @endif
    </div>
</article>
