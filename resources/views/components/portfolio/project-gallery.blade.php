@props(['project'])

@php
    $images = $project->media
        ->filter(fn ($media) => $media->is_image)
        ->values();
@endphp

@if ($images->isNotEmpty())
    <section
        class="mt-16 border-t border-slate-200 pt-10 sm:mt-20 sm:pt-12"
        aria-labelledby="project-gallery-title"
        x-data="{
            image: '',
            alt: '',
            open(src, description) {
                this.image = src;
                this.alt = description;
                this.$refs.dialog.showModal();
            },
            close() {
                this.$refs.dialog.close();
            }
        }"
    >
        <div class="mb-6 flex items-end justify-between gap-4">
            <div>
                <p class="font-mono text-[0.68rem] font-semibold uppercase tracking-[0.2em] text-sky-700">Archivo visual</p>
                <h2 id="project-gallery-title" class="mt-2 text-2xl font-semibold tracking-[-0.025em] text-slate-950">
                    Capturas del proyecto
                </h2>
            </div>
            <p class="font-mono text-xs text-slate-400">{{ str_pad((string) $images->count(), 2, '0', STR_PAD_LEFT) }} imágenes</p>
        </div>

        <div class="grid gap-4 sm:grid-cols-2">
            @foreach ($images as $image)
                @php
                    $imageUrl = $image->url;
                    $imageAlt = $image->alt ?: 'Captura de '.$project->title;
                @endphp

                <button
                    type="button"
                    class="group relative aspect-video overflow-hidden rounded-2xl border border-slate-200 bg-slate-100 text-left shadow-[0_16px_35px_-30px_rgba(15,23,42,0.5)] focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 focus-visible:ring-offset-2"
                    x-on:click="open(@js($imageUrl), @js($imageAlt))"
                    aria-label="Ampliar: {{ $imageAlt }}"
                >
                    <img
                        src="{{ $imageUrl }}"
                        alt="{{ $imageAlt }}"
                        width="1200"
                        height="675"
                        loading="lazy"
                        decoding="async"
                        class="h-full w-full object-cover transition duration-300 group-hover:scale-[1.02]"
                    >
                    <span class="absolute inset-x-3 bottom-3 inline-flex w-fit rounded-lg bg-slate-950/85 px-3 py-2 font-mono text-[0.68rem] font-semibold uppercase tracking-[0.12em] text-white opacity-100 backdrop-blur sm:opacity-0 sm:transition-opacity sm:group-hover:opacity-100">
                        Ampliar
                    </span>
                </button>
            @endforeach
        </div>

        <dialog
            x-ref="dialog"
            x-on:click="if ($event.target === $refs.dialog) close()"
            class="m-auto max-h-[92vh] w-[min(94vw,80rem)] overflow-hidden rounded-2xl bg-slate-950 p-0 text-white shadow-2xl backdrop:bg-slate-950/85 backdrop:backdrop-blur-sm"
            aria-labelledby="project-lightbox-title"
        >
            <div class="flex items-center justify-between gap-4 border-b border-white/10 px-4 py-3 sm:px-5">
                <p id="project-lightbox-title" class="truncate text-sm font-medium" x-text="alt"></p>
                <button
                    type="button"
                    x-on:click="close()"
                    class="inline-flex size-11 shrink-0 items-center justify-center rounded-xl text-xl text-slate-300 transition-colors hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400"
                    aria-label="Cerrar imagen ampliada"
                >
                    <span aria-hidden="true">×</span>
                </button>
            </div>
            <div class="flex max-h-[calc(92vh-4.25rem)] items-center justify-center p-3 sm:p-5">
                <img x-bind:src="image" x-bind:alt="alt" class="max-h-[calc(92vh-6.5rem)] w-auto rounded-xl object-contain">
            </div>
        </dialog>
    </section>
@endif
