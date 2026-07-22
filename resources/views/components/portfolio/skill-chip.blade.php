@props(['skill'])

<div class="group flex min-h-12 items-center justify-between gap-4 rounded-2xl border border-ink-950/8 bg-white/70 px-4 py-3 shadow-[0_1px_2px_rgba(9,18,35,.03)] transition-[transform,border-color,background-color] duration-200 hover:-translate-y-0.5 hover:border-signal-400/50 hover:bg-white">
    <div class="flex min-w-0 items-center gap-3">
        <x-portfolio.skill-icon :icon="$skill->icon" :name="$skill->name" />
        <div class="min-w-0">
            <p class="truncate text-sm font-semibold text-ink-900">{{ $skill->name }}</p>
            <p class="mt-0.5 font-mono text-[0.64rem] uppercase tracking-[0.14em] text-ink-400">Nivel {{ $skill->level }} de 5</p>
        </div>
    </div>
    <div class="flex shrink-0 gap-1" role="img" aria-label="Nivel {{ $skill->level }} de 5">
        @for ($level = 1; $level <= 5; $level++)
            <span class="h-1.5 w-3 rounded-full {{ $level <= $skill->level ? 'bg-signal-500' : 'bg-ink-950/10' }}" aria-hidden="true"></span>
        @endfor
    </div>
</div>
