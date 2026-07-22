@props([
    'media' => collect(),
    'uploads' => [],
    'limit' => 8,
    'uploadModel' => 'galleryImages',
    'allowCover' => true,
    'title' => 'Galería de imágenes',
    'description' => 'Añade evidencia visual, ordénala y define una portada.',
    'emptyText' => 'Todavía no hay imágenes en esta galería.',
])

@php
    $existingMedia = collect($media)->values();
    $pendingUploads = collect($uploads)->values();
    $total = $existingMedia->count() + $pendingUploads->count();
    $editorId = $attributes->get('id', 'media-gallery-editor');
@endphp

<section
    class="space-y-4 rounded-xl border border-zinc-200 bg-white p-4 dark:border-zinc-700 dark:bg-zinc-950"
    aria-labelledby="{{ $editorId }}-title"
    x-data="{
        image: '',
        alt: '',
        open(src, description) {
            this.image = src;
            this.alt = description;
            this.$refs.galleryDialog.showModal();
        },
        close() {
            this.$refs.galleryDialog.close();
        }
    }"
>
    <div class="flex flex-col gap-3 sm:flex-row sm:items-start sm:justify-between">
        <div>
            <div class="flex flex-wrap items-center gap-2">
                <h3 id="{{ $editorId }}-title" class="text-sm font-semibold text-zinc-950 dark:text-white">{{ $title }}</h3>
                <span class="rounded-full bg-zinc-100 px-2 py-0.5 font-mono text-[0.65rem] font-semibold tabular-nums text-zinc-500 dark:bg-zinc-800 dark:text-zinc-300">{{ $total }}/{{ $limit }}</span>
            </div>
            <p class="mt-1 text-xs leading-5 text-zinc-500">{{ $description }}</p>
        </div>

        <label class="inline-flex min-h-10 cursor-pointer items-center justify-center gap-2 rounded-lg bg-zinc-950 px-3 text-sm font-medium text-white transition-[background-color,transform] hover:bg-zinc-800 active:scale-[.98] focus-within:ring-2 focus-within:ring-sky-500 focus-within:ring-offset-2 dark:bg-white dark:text-zinc-950 dark:hover:bg-zinc-200 dark:focus-within:ring-offset-zinc-950">
            <flux:icon.photo class="size-4" />
            Añadir fotos
            <input type="file" wire:model="{{ $uploadModel }}" accept="image/jpeg,image/png,image/webp" multiple class="sr-only">
        </label>
    </div>

    <div wire:loading.flex wire:target="{{ $uploadModel }}" class="items-center gap-2 rounded-lg bg-zinc-50 px-3 py-2 text-xs text-zinc-500 dark:bg-zinc-900">
        <flux:icon.loading class="size-4" /> Preparando vistas previas…
    </div>

    @error($uploadModel)
        <p class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    @foreach ($errors->get($uploadModel.'.*') as $messages)
        @foreach ($messages as $message)
            <p class="text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
        @endforeach
    @endforeach

    @if ($existingMedia->isNotEmpty())
        <div class="grid gap-3 sm:grid-cols-2" wire:sort="sortGalleryImage" aria-label="Imágenes guardadas. Arrastra el control para reordenar.">
            @foreach ($existingMedia as $mediaItem)
                @php($mediaAltValue = $mediaItem->alt ?: $mediaItem->filename)
                <article wire:key="gallery-media-{{ $mediaItem->id }}" wire:sort:item="{{ $mediaItem->id }}" class="group/media overflow-hidden rounded-xl border border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
                    <div class="relative aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                        <button type="button" wire:sort:ignore class="absolute inset-0 size-full cursor-zoom-in focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500" x-on:click="open(@js($mediaItem->url), @js($mediaAltValue))" aria-label="Ampliar {{ $mediaAltValue }}">
                            <img src="{{ $mediaItem->url }}" alt="{{ $mediaAltValue }}" class="size-full object-cover transition-transform duration-200 group-hover/media:scale-[1.02]">
                        </button>

                        <div class="absolute inset-x-2 top-2 flex items-center justify-between gap-2">
                            <button type="button" wire:sort:handle class="grid size-9 cursor-grab place-items-center rounded-lg bg-zinc-950/85 text-white backdrop-blur active:cursor-grabbing" aria-label="Arrastrar para reordenar"><flux:icon.bars-3 class="size-4" /></button>
                            <div class="flex gap-1">
                                @if ($allowCover)
                                    <button type="button" wire:click="useMediaAsCover({{ $mediaItem->id }})" class="inline-flex min-h-9 items-center gap-1.5 rounded-lg bg-white/95 px-2.5 text-xs font-semibold text-zinc-800 shadow-sm hover:bg-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500" aria-label="Usar como portada"><flux:icon.star class="size-3.5" /> Portada</button>
                                @endif
                                <button type="button" wire:click="confirmMediaDelete({{ $mediaItem->id }})" class="grid size-9 place-items-center rounded-lg bg-red-600/95 text-white shadow-sm hover:bg-red-500 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-red-300" aria-label="Eliminar {{ $mediaAltValue }}"><flux:icon.trash class="size-4" /></button>
                            </div>
                        </div>
                    </div>

                    <div class="grid grid-cols-[minmax(0,1fr)_auto] gap-2 p-3">
                        <label class="min-w-0">
                            <span class="sr-only">Texto alternativo de {{ $mediaItem->filename }}</span>
                            <input type="text" wire:model="mediaAlt.{{ $mediaItem->id }}" maxlength="255" placeholder="Describe lo que aparece" class="min-h-9 w-full rounded-lg border border-zinc-200 bg-white px-2.5 text-xs text-zinc-800 outline-none focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-zinc-700 dark:bg-zinc-950 dark:text-zinc-100">
                        </label>
                        <flux:button type="button" size="sm" variant="ghost" icon="check" wire:click="saveMediaAlt({{ $mediaItem->id }})" aria-label="Guardar texto alternativo" />
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @if ($pendingUploads->isNotEmpty())
        <div class="space-y-2">
            <p class="text-[0.65rem] font-semibold uppercase tracking-[0.14em] text-sky-700 dark:text-sky-300">Listas para guardar</p>
            <div class="grid gap-3 sm:grid-cols-2">
                @foreach ($pendingUploads as $index => $upload)
                    <figure wire:key="pending-gallery-upload-{{ $index }}" class="overflow-hidden rounded-xl border border-dashed border-sky-300 bg-sky-50/60 dark:border-sky-700 dark:bg-sky-950/20">
                        <div class="relative aspect-video overflow-hidden bg-zinc-100 dark:bg-zinc-800">
                            @if ($upload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $upload->isPreviewable())
                                <img src="{{ $upload->temporaryUrl() }}" alt="Vista previa de {{ $upload->getClientOriginalName() }}" class="size-full object-cover">
                            @else
                                <div class="grid size-full place-items-center text-xs text-zinc-500">Vista previa no disponible</div>
                            @endif
                            <button type="button" wire:click="removePendingGalleryImage({{ $index }})" class="absolute right-2 top-2 grid size-9 place-items-center rounded-lg bg-zinc-950/85 text-white backdrop-blur hover:bg-red-600 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white" aria-label="Quitar {{ $upload->getClientOriginalName() }}"><flux:icon.x-mark class="size-4" /></button>
                        </div>
                        <figcaption class="truncate px-3 py-2 text-xs text-zinc-600 dark:text-zinc-300">{{ $upload->getClientOriginalName() }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    @endif

    @if ($existingMedia->isEmpty() && $pendingUploads->isEmpty())
        <div class="rounded-xl border border-dashed border-zinc-300 px-4 py-8 text-center dark:border-zinc-700">
            <flux:icon.photo class="mx-auto size-5 text-zinc-400" />
            <p class="mt-2 text-sm font-medium text-zinc-700 dark:text-zinc-200">{{ $emptyText }}</p>
            <p class="mt-1 text-xs text-zinc-500">Puedes seleccionar varias fotos en una sola carga.</p>
        </div>
    @endif

    <dialog x-ref="galleryDialog" x-on:click="if ($event.target === $refs.galleryDialog) close()" class="m-auto max-h-[92vh] w-[min(94vw,76rem)] overflow-hidden rounded-2xl bg-zinc-950 p-0 text-white shadow-2xl backdrop:bg-zinc-950/85 backdrop:backdrop-blur-sm">
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-4 py-3">
            <p class="truncate text-sm font-medium" x-text="alt"></p>
            <button type="button" x-on:click="close()" class="grid size-11 place-items-center rounded-xl text-xl text-zinc-300 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400" aria-label="Cerrar imagen ampliada">×</button>
        </div>
        <div class="flex max-h-[calc(92vh-4.25rem)] items-center justify-center p-3 sm:p-5">
            <img x-bind:src="image" x-bind:alt="alt" class="max-h-[calc(92vh-6.5rem)] w-auto rounded-xl object-contain">
        </div>
    </dialog>
</section>
