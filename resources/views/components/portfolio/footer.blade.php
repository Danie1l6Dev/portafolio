<footer class="border-t border-ink-950/8 bg-ink-950 text-white">
    <div class="portfolio-container grid gap-10 py-12 md:grid-cols-[1.3fr_.7fr] md:items-end">
        <div>
            <div class="mb-5 inline-flex items-center gap-3">
                <img src="{{ asset('favicon.svg') }}?v=2" alt="" width="36" height="36" class="size-9 rounded-xl" aria-hidden="true">
                <span class="font-semibold">{{ config('portfolio.name') }}</span>
            </div>
            <p class="max-w-xl text-balance text-sm leading-7 text-white/60">{{ config('portfolio.description') }}</p>
        </div>

        <div class="md:text-right">
            <div class="mb-5 flex flex-wrap gap-2 md:justify-end">
                @foreach (config('portfolio.socials') as $social)
                    <a href="{{ $social['url'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-11 items-center gap-2 rounded-xl border border-white/12 px-3.5 text-sm text-white/70 transition-colors hover:border-white/25 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-400">
                        <x-portfolio.social-icon :name="$social['icon']" class="size-4" />
                        {{ $social['name'] }}
                    </a>
                @endforeach
            </div>
            <p class="font-mono text-[0.7rem] uppercase tracking-[0.18em] text-white/40">© {{ now()->year }} · Hecho con Laravel</p>
        </div>
    </div>
</footer>
