@props(['index', 'eyebrow', 'title', 'description' => null, 'align' => 'left'])

<div {{ $attributes->class(['mb-10 md:mb-14', 'text-center mx-auto max-w-2xl' => $align === 'center']) }}>
    <div class="mb-4 flex items-center gap-3 {{ $align === 'center' ? 'justify-center' : '' }}">
        <span class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-signal-700 dark:text-signal-300">{{ $index }}</span>
        <span class="h-px w-8 bg-signal-400" aria-hidden="true"></span>
        <span class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-ink-500 dark:text-slate-400">{{ $eyebrow }}</span>
    </div>
    <h2 class="text-balance text-3xl font-semibold tracking-[-0.035em] text-ink-950 dark:text-white sm:text-4xl lg:text-5xl">{{ $title }}</h2>
    @if ($description)
        <p class="mt-5 max-w-2xl text-pretty text-base leading-8 text-ink-600 dark:text-slate-300 sm:text-lg">{{ $description }}</p>
    @endif
</div>
