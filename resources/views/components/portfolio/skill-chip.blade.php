@props(['skill'])

<div class="group flex min-h-14 items-center gap-3 rounded-2xl border border-ink-950/8 bg-white/70 px-4 py-3 shadow-[0_1px_2px_rgba(9,18,35,.03)] transition-[transform,border-color,background-color] duration-200 hover:-translate-y-0.5 hover:border-signal-400/50 hover:bg-white dark:border-white/10 dark:bg-white/[.045] dark:hover:border-signal-400/45 dark:hover:bg-white/[.075]">
    <x-portfolio.skill-icon :icon="$skill->icon" :name="$skill->name" />
    <p class="min-w-0 truncate text-sm font-semibold text-ink-900 dark:text-slate-100">{{ $skill->name }}</p>
</div>
