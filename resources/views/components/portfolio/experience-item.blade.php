@props(['experience', 'index'])

<article class="relative grid gap-4 border-l border-ink-950/12 pb-10 pl-8 dark:border-white/15 last:pb-0 sm:grid-cols-[9rem_1fr] sm:gap-8 sm:pl-10">
    <span class="absolute -left-1.5 top-1.5 size-3 rounded-full border-2 border-paper-50 bg-signal-500 ring-4 ring-signal-500/10 dark:border-[#07111f] dark:bg-signal-400" aria-hidden="true"></span>
    <div class="font-mono text-xs uppercase tracking-[0.12em] text-ink-400 dark:text-slate-400">{{ $experience->duration }}</div>
    <div>
        <h3 class="text-xl font-semibold tracking-tight text-ink-950 dark:text-white">{{ $experience->position }}</h3>
        <p class="mt-1 text-sm font-medium text-signal-700 dark:text-signal-300">{{ $experience->company }}</p>
        @if ($experience->location)
            <p class="mt-1 text-sm text-ink-400 dark:text-slate-400">{{ $experience->location }}</p>
        @endif
        @if ($experience->description)
            <p class="mt-4 max-w-2xl text-pretty text-sm leading-7 text-ink-600 dark:text-slate-300">{{ $experience->description }}</p>
        @endif
    </div>
</article>
