@props(['achievement'])

@php
    $images = $achievement->media
        ->filter(fn ($media) => $media->is_image)
        ->values();
@endphp

@if ($images->isNotEmpty())
    <section
        class="mt-6 border-t border-ink-950/8 pt-5 dark:border-white/10"
        aria-label="Galería fotográfica de {{ $achievement->title }}"
        x-data="{
            image: '',
            alt: '',
            open(src, description) {
                this.image = src;
                this.alt = description;
                this.$refs.achievementDialog.showModal();
            },
            close() {
                this.$refs.achievementDialog.close();
            }
        }"
    >
        <div class="mb-3 flex items-center justify-between gap-4">
            <p class="font-mono text-[0.6rem] font-semibold uppercase tracking-[0.16em] text-ink-400 dark:text-slate-500">Archivo visual</p>
            <p class="font-mono text-[0.62rem] tabular-nums text-ink-400 dark:text-slate-500">{{ str_pad((string) $images->count(), 2, '0', STR_PAD_LEFT) }} fotos</p>
        </div>

        <div class="flex snap-x gap-2 overflow-x-auto pb-2">
            @foreach ($images as $image)
                @php($imageAlt = $image->alt ?: 'Evidencia de '.$achievement->title)
                <button
                    type="button"
                    class="group/gallery relative aspect-[4/3] w-28 shrink-0 snap-start overflow-hidden rounded-xl border border-ink-950/10 bg-paper-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-500 dark:border-white/10 dark:bg-white/[.06]"
                    x-on:click="open(@js($image->url), @js($imageAlt))"
                    aria-label="Ampliar {{ $imageAlt }}"
                >
                    <img src="{{ $image->url }}" alt="{{ $imageAlt }}" loading="lazy" decoding="async" class="size-full object-cover transition-transform duration-200 group-hover/gallery:scale-[1.04]">
                    <span class="absolute inset-0 ring-1 ring-inset ring-black/10 dark:ring-white/10" aria-hidden="true"></span>
                </button>
            @endforeach
        </div>

        <dialog
            x-ref="achievementDialog"
            x-on:click="if ($event.target === $refs.achievementDialog) close()"
            class="m-auto max-h-[92vh] w-[min(94vw,76rem)] overflow-hidden rounded-2xl bg-[#040b16] p-0 text-white shadow-2xl backdrop:bg-[#040b16]/90 backdrop:backdrop-blur-sm"
        >
            <div class="flex items-center justify-between gap-4 border-b border-white/10 px-4 py-3 sm:px-5">
                <p class="truncate text-sm font-medium" x-text="alt"></p>
                <button type="button" x-on:click="close()" class="grid size-11 place-items-center rounded-xl text-xl text-white/65 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-signal-400" aria-label="Cerrar imagen ampliada">×</button>
            </div>
            <div class="flex max-h-[calc(92vh-4.25rem)] items-center justify-center p-3 sm:p-5">
                <img x-bind:src="image" x-bind:alt="alt" class="max-h-[calc(92vh-6.5rem)] w-auto rounded-xl object-contain">
            </div>
        </dialog>
    </section>
@endif
