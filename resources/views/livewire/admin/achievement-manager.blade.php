<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div>
            <flux:heading size="xl" level="1">Logros y reconocimientos</flux:heading>
            <flux:text variant="subtle">Administra hackathons, certificaciones, premios y la evidencia que los respalda.</flux:text>
        </div>
        <flux:button variant="primary" icon="plus" wire:click="create">Nuevo logro</flux:button>
    </header>

    <div class="grid gap-6 {{ $showForm ? 'xl:grid-cols-[minmax(0,1fr)_34rem]' : '' }}">
        <section class="min-w-0 space-y-4" aria-label="Listado de logros">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_12rem_11rem]">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar título, organización o resultado" aria-label="Buscar logros" />
                <select wire:model.live="typeFilter" class="min-h-10 rounded-lg border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" aria-label="Filtrar por tipo">
                    <option value="">Todos los tipos</option>
                    @foreach ($types as $typeOption)
                        <option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</option>
                    @endforeach
                </select>
                <select wire:model.live="visibilityFilter" class="min-h-10 rounded-lg border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900" aria-label="Filtrar por visibilidad">
                    <option value="all">Todos</option>
                    <option value="visible">Visibles</option>
                    <option value="hidden">Ocultos</option>
                </select>
            </div>

            <div wire:loading.flex wire:target="search,typeFilter,visibilityFilter,save,delete" class="items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900">
                <flux:icon.loading class="size-4" /> Actualizando logros…
            </div>

            <div class="grid gap-4 lg:grid-cols-2">
                @forelse ($achievements as $achievement)
                    <article wire:key="achievement-{{ $achievement->id }}" class="overflow-hidden rounded-xl border border-zinc-200/80 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                        @if ($achievement->imageUrl())
                            <img src="{{ $achievement->imageUrl() }}" alt="Evidencia visual de {{ $achievement->title }}" class="aspect-[16/7] w-full object-cover">
                        @endif
                        <div class="p-5">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="rounded-full bg-sky-50 px-2 py-0.5 text-[11px] font-semibold text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">{{ $achievement->type->label() }}</span>
                                @if ($achievement->is_featured)<span class="rounded-full bg-violet-50 px-2 py-0.5 text-[11px] font-semibold text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">Destacado</span>@endif
                                @unless ($achievement->is_visible)<span class="rounded-full bg-zinc-100 px-2 py-0.5 text-[11px] font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">Oculto</span>@endunless
                            </div>
                            <flux:heading size="lg" class="mt-3">{{ $achievement->title }}</flux:heading>
                            <p class="mt-1 text-sm font-medium text-zinc-600 dark:text-zinc-300">{{ $achievement->organization }}@if($achievement->result) · {{ $achievement->result }}@endif</p>
                            <p class="mt-3 line-clamp-2 text-sm leading-6 text-zinc-500">{{ $achievement->description ?: 'Sin descripción registrada.' }}</p>
                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-zinc-200/70 pt-4 dark:border-zinc-700">
                                <div class="flex items-center gap-2 text-xs text-zinc-500">
                                    <time datetime="{{ $achievement->achieved_at->toDateString() }}">{{ $achievement->achieved_at->translatedFormat('M Y') }}</time>
                                    @if ($achievement->certificate_path)<span aria-label="Incluye certificado PDF">· PDF</span>@endif
                                    @if ($achievement->media->isNotEmpty())<span aria-label="Fotos en la galería">· {{ $achievement->media->count() }} fotos</span>@endif
                                </div>
                                <div class="flex items-center gap-1">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $achievement->id }})" aria-label="Editar {{ $achievement->title }}" />
                                    <flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $achievement->id }})" aria-label="Eliminar {{ $achievement->title }}" />
                                </div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-14 text-center dark:border-zinc-700 lg:col-span-2">
                        <flux:heading size="lg">Tu vitrina de logros está lista</flux:heading>
                        <flux:text variant="subtle" class="mt-1">Añade la hackathon, certificación o reconocimiento que quieras respaldar.</flux:text>
                    </div>
                @endforelse
            </div>

            {{ $achievements->links() }}
        </section>

        @if ($showForm)
            <aside class="h-fit rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-900" aria-label="Formulario de logro">
                <div class="mb-5">
                    <flux:heading size="lg">{{ $editingAchievementId ? 'Editar logro' : 'Nuevo logro' }}</flux:heading>
                    <flux:text variant="subtle" class="text-sm">Registra el resultado, tu aporte y una evidencia verificable.</flux:text>
                </div>

                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="title" label="Título" required autofocus />
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                        <label class="grid gap-2 text-sm font-medium">
                            <span>Tipo</span>
                            <select wire:model="type" class="min-h-10 rounded-lg border border-zinc-200 bg-white px-3 dark:border-zinc-700 dark:bg-zinc-950">
                                @foreach ($types as $typeOption)
                                    <option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</option>
                                @endforeach
                            </select>
                            @error('type')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                        </label>
                        <flux:input wire:model="achievedAt" label="Fecha" type="date" required />
                    </div>
                    <flux:input wire:model="organization" label="Evento u organización" required />
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1">
                        <flux:input wire:model="result" label="Resultado" placeholder="Ganador, finalista…" />
                        <flux:input wire:model="role" label="Tu rol" placeholder="Backend, liderazgo…" />
                    </div>
                    <flux:textarea wire:model="description" label="Descripción" rows="5" maxlength="3000" />
                    <flux:input wire:model="externalUrl" label="Enlace de evidencia" type="url" placeholder="https://" />

                    <div class="space-y-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-950">
                        <flux:input wire:model="image" label="Portada" type="file" accept="image/jpeg,image/png,image/webp" />
                        <div wire:loading wire:target="image" class="text-xs text-zinc-500">Procesando imagen…</div>
                        @if ($image)
                            <p class="text-xs text-zinc-500">Nueva imagen: {{ $image->getClientOriginalName() }}</p>
                        @elseif($editingAchievement?->image_path && ! $removeCurrentImage)
                            <div class="flex items-center gap-3">
                                <img src="{{ $editingAchievement->imageUrl() }}" alt="Imagen actual" class="h-14 w-20 rounded-lg object-cover">
                                <flux:button type="button" size="sm" variant="ghost" wire:click="markImageForRemoval">Quitar</flux:button>
                            </div>
                        @elseif($removeCurrentImage)
                            <p class="text-xs text-amber-600 dark:text-amber-400">La imagen actual se eliminará al guardar.</p>
                        @endif
                    </div>

                    <x-admin.media-gallery-editor
                        id="achievement-media-gallery"
                        :media="$editingAchievement?->media ?? collect()"
                        :uploads="$galleryImages"
                        :limit="$galleryLimit"
                        title="Fotos del logro"
                        description="Guarda varias fotos de la hackathon, el equipo, la premiación o la evidencia del certificado."
                        empty-text="Añade las fotos que ayuden a contar qué ocurrió y cuál fue el resultado."
                    />

                    <div class="space-y-3 rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-950">
                        <flux:input wire:model="certificate" label="Certificado PDF" type="file" accept="application/pdf" />
                        <div wire:loading wire:target="certificate" class="text-xs text-zinc-500">Procesando documento…</div>
                        @if ($certificate)
                            <p class="text-xs text-zinc-500">Nuevo documento: {{ $certificate->getClientOriginalName() }}</p>
                        @elseif($editingAchievement?->certificate_path && ! $removeCurrentCertificate)
                            <div class="flex items-center justify-between gap-3 text-sm">
                                <a href="{{ $editingAchievement->certificateUrl() }}" target="_blank" rel="noopener noreferrer" class="font-medium text-sky-600 hover:underline">Ver PDF actual</a>
                                <flux:button type="button" size="sm" variant="ghost" wire:click="markCertificateForRemoval">Quitar</flux:button>
                            </div>
                        @elseif($removeCurrentCertificate)
                            <p class="text-xs text-amber-600 dark:text-amber-400">El PDF actual se eliminará al guardar.</p>
                        @endif
                    </div>

                    <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-1">
                        <label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-3 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><input type="checkbox" wire:model="isFeatured" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600"><span>Destacar primero</span></label>
                        <label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-3 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><input type="checkbox" wire:model="isVisible" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600"><span>Visible en el portafolio</span></label>
                    </div>
                    <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" max="65535" />

                    <div class="flex justify-end gap-2 pt-2">
                        <flux:button type="button" variant="ghost" wire:click="cancelForm">Cancelar</flux:button>
                        <flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,image,certificate,galleryImages">Guardar</flux:button>
                    </div>
                </form>
            </aside>
        @endif
    </div>

    <flux:modal name="delete-achievement" wire:model="confirmingDelete" class="max-w-md">
        <div class="space-y-5">
            <div><flux:heading size="lg">Eliminar logro</flux:heading><flux:text variant="subtle" class="mt-1">Se eliminará “{{ $deletingAchievementTitle }}” junto con su imagen y certificado.</flux:text></div>
            <div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</flux:button></div>
        </div>
    </flux:modal>
    <flux:modal name="delete-achievement-media" wire:model="confirmingMediaDelete" class="max-w-md">
        <div class="space-y-5">
            <div><flux:heading size="lg">Quitar foto</flux:heading><flux:text variant="subtle" class="mt-1">“{{ $deletingMediaName }}” se eliminará de la galería y del almacenamiento.</flux:text></div>
            <div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelMediaDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="deleteMedia" wire:loading.attr="disabled" wire:target="deleteMedia">Quitar foto</flux:button></div>
        </div>
    </flux:modal>
</div>
