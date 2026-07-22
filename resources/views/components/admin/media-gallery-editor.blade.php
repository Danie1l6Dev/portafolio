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
    id="{{ $editorId }}"
    {{ $attributes->except('id')->class('admin-gallery') }}
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
    <div class="admin-gallery__header">
        <div class="min-w-0">
            <div class="flex flex-wrap items-center gap-2">
                <h3 id="{{ $editorId }}-title" class="admin-gallery__title">{{ $title }}</h3>
                <span class="admin-gallery__count">{{ $total }}/{{ $limit }}</span>
            </div>
            <p class="admin-gallery__description">{{ $description }}</p>
        </div>

        <label class="admin-gallery__upload">
            <flux:icon.photo class="size-4" />
            Añadir fotos
            <input
                type="file"
                wire:model="{{ $uploadModel }}"
                accept="image/jpeg,image/png,image/webp"
                multiple
                class="sr-only"
            >
        </label>
    </div>

    <div wire:loading.flex wire:target="{{ $uploadModel }}" class="admin-loading mt-4">
        <flux:icon.loading class="size-4" />
        Preparando vistas previas…
    </div>

    @error($uploadModel)
        <p class="mt-3 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    @error('gallery')
        <p class="mt-3 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
    @enderror
    @foreach ($errors->get($uploadModel.'.*') as $messages)
        @foreach ($messages as $message)
            <p class="mt-3 text-xs font-medium text-red-600 dark:text-red-400">{{ $message }}</p>
        @endforeach
    @endforeach

    @if ($existingMedia->isNotEmpty())
        <div class="admin-gallery__grid" wire:sort="sortGalleryImage" aria-label="Imágenes guardadas. Arrastra el control para reordenar.">
            @foreach ($existingMedia as $mediaItem)
                @php($mediaAltValue = $mediaItem->alt ?: $mediaItem->filename)
                <article
                    wire:key="gallery-media-{{ $mediaItem->id }}"
                    wire:sort:item="{{ $mediaItem->id }}"
                    class="admin-gallery__item group/gallery"
                >
                    <div class="admin-gallery__visual">
                        <button
                            type="button"
                            wire:sort:ignore
                            class="absolute inset-0 size-full cursor-zoom-in focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-sky-500"
                            x-on:click="open(@js($mediaItem->url), @js($mediaAltValue))"
                            aria-label="Ampliar {{ $mediaAltValue }}"
                        >
                            <img src="{{ $mediaItem->url }}" alt="{{ $mediaAltValue }}" class="size-full object-cover transition-transform duration-200 group-hover/gallery:scale-[1.03]">
                        </button>

                        <div class="admin-gallery__overlay" aria-hidden="true"></div>

                        <button type="button" wire:sort:handle class="admin-gallery__handle" aria-label="Arrastrar para reordenar">
                            <flux:icon.bars-3 class="size-4" />
                        </button>

                        <div class="admin-gallery__actions">
                            @if ($allowCover)
                                <button
                                    type="button"
                                    wire:click="useMediaAsCover({{ $mediaItem->id }})"
                                    class="admin-gallery__action admin-gallery__action--cover"
                                    aria-label="Usar {{ $mediaAltValue }} como portada"
                                    title="Usar como portada"
                                >
                                    <flux:icon.star class="size-4" />
                                </button>
                            @endif
                            <button
                                type="button"
                                wire:click="confirmMediaDelete({{ $mediaItem->id }})"
                                class="admin-gallery__action admin-gallery__action--danger"
                                aria-label="Eliminar {{ $mediaAltValue }}"
                                title="Eliminar imagen"
                            >
                                <flux:icon.trash class="size-4" />
                            </button>
                        </div>
                    </div>

                    <div class="admin-gallery__caption">
                        <label class="min-w-0 flex-1">
                            <span class="sr-only">Texto alternativo de {{ $mediaItem->filename }}</span>
                            <input
                                type="text"
                                wire:model="mediaAlt.{{ $mediaItem->id }}"
                                maxlength="255"
                                placeholder="Describe la imagen"
                                class="admin-gallery__alt"
                            >
                        </label>
                        <button
                            type="button"
                            wire:click="saveMediaAlt({{ $mediaItem->id }})"
                            class="admin-gallery__save-alt"
                            aria-label="Guardar texto alternativo"
                            title="Guardar descripción"
                        >
                            <flux:icon.check class="size-4" />
                        </button>
                    </div>
                </article>
            @endforeach
        </div>
    @endif

    @if ($pendingUploads->isNotEmpty())
        <div class="mt-5">
            <p class="admin-gallery__pending-label">Listas para guardar</p>
            <div class="admin-gallery__grid mt-2">
                @foreach ($pendingUploads as $index => $upload)
                    <figure wire:key="pending-gallery-upload-{{ $index }}" class="admin-gallery__item admin-gallery__item--pending">
                        <div class="admin-gallery__visual">
                            @if ($upload instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $upload->isPreviewable())
                                <img src="{{ $upload->temporaryUrl() }}" alt="Vista previa de {{ $upload->getClientOriginalName() }}" class="size-full object-cover">
                            @else
                                <div class="grid size-full place-items-center px-3 text-center text-xs text-slate-500 dark:text-slate-400">Vista previa no disponible</div>
                            @endif
                            <span class="admin-gallery__pending-badge">Pendiente</span>
                            <button
                                type="button"
                                wire:click="removePendingGalleryImage({{ $index }})"
                                class="admin-gallery__remove-pending"
                                aria-label="Quitar {{ $upload->getClientOriginalName() }}"
                            >
                                <flux:icon.x-mark class="size-4" />
                            </button>
                        </div>
                        <figcaption class="truncate px-3 py-2.5 text-xs text-slate-600 dark:text-slate-300">{{ $upload->getClientOriginalName() }}</figcaption>
                    </figure>
                @endforeach
            </div>
        </div>
    @endif

    @if ($existingMedia->isEmpty() && $pendingUploads->isEmpty())
        <div class="admin-gallery__empty">
            <span class="admin-gallery__empty-icon"><flux:icon.photo class="size-5" /></span>
            <p class="mt-3 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $emptyText }}</p>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Puedes seleccionar varias fotos en una sola carga.</p>
        </div>
    @endif

    <dialog
        x-ref="galleryDialog"
        x-on:click="if ($event.target === $refs.galleryDialog) close()"
        x-on:keydown.escape.prevent="close()"
        class="m-auto max-h-[92vh] w-[min(94vw,76rem)] overflow-hidden rounded-2xl bg-slate-950 p-0 text-white shadow-2xl backdrop:bg-slate-950/85 backdrop:backdrop-blur-sm"
    >
        <div class="flex items-center justify-between gap-4 border-b border-white/10 px-4 py-3">
            <p class="truncate text-sm font-medium" x-text="alt"></p>
            <button type="button" x-on:click="close()" class="grid size-10 place-items-center rounded-lg text-xl text-slate-300 hover:bg-white/10 hover:text-white focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-400" aria-label="Cerrar imagen ampliada">×</button>
        </div>
        <div class="flex max-h-[calc(92vh-4.25rem)] items-center justify-center p-3 sm:p-5">
            <img x-bind:src="image" x-bind:alt="alt" class="max-h-[calc(92vh-6.5rem)] w-auto rounded-xl object-contain">
        </div>
    </dialog>
</section>
