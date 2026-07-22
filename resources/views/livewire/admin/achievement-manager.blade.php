<div class="admin-page">
    <x-admin.page-header
        title="Logros y reconocimientos"
        description="Documenta certificaciones, premios, hackathons y la evidencia que los respalda."
        :count="$achievements->total()"
    >
        <x-slot:actions>
            <x-admin.button variant="primary" wire:click="create">
                <flux:icon.plus class="size-4" />
                Nuevo logro
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-toolbar" aria-label="Filtros de logros">
        <div class="min-w-0 flex-1 lg:max-w-md">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Buscar título, organización o resultado"
                aria-label="Buscar logros"
            />
        </div>
        <div class="w-full sm:w-52">
            <flux:select wire:model.live="typeFilter" aria-label="Filtrar por tipo">
                <flux:select.option value="">Todos los tipos</flux:select.option>
                @foreach ($types as $typeOption)
                    <flux:select.option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
        <div class="admin-filter-chips" role="group" aria-label="Filtrar por visibilidad">
            @foreach (['all' => 'Todos', 'visible' => 'Visibles', 'hidden' => 'Ocultos'] as $value => $label)
                <button
                    type="button"
                    wire:click="$set('visibilityFilter', '{{ $value }}')"
                    class="admin-filter-chip {{ $visibilityFilter === $value ? 'is-active' : '' }}"
                    aria-pressed="{{ $visibilityFilter === $value ? 'true' : 'false' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </section>

    <div wire:loading.flex wire:target="search,typeFilter,visibilityFilter,save,delete" class="admin-loading">
        <flux:icon.loading class="size-4" />
        Actualizando archivo de logros…
    </div>

    <section class="admin-table-shell hidden lg:block" aria-label="Listado de logros">
        <table class="admin-table">
            <thead>
                <tr>
                    <th scope="col">Logro</th>
                    <th scope="col">Tipo</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Evidencia</th>
                    <th scope="col">Visibilidad</th>
                    <th scope="col"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($achievements as $achievement)
                    <tr wire:key="achievement-row-{{ $achievement->id }}">
                        <td>
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="admin-table__thumb">
                                    @if ($achievement->imageUrl())
                                        <img src="{{ $achievement->imageUrl() }}" alt="" class="size-full object-cover">
                                    @else
                                        <flux:icon.trophy class="size-4" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ $achievement->title }}</p>
                                        @if ($achievement->is_featured)
                                            <span class="text-amber-500" title="Logro destacado" aria-label="Logro destacado">★</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 max-w-sm truncate text-xs text-slate-500 dark:text-slate-400">
                                        {{ $achievement->organization }}@if($achievement->result) · {{ $achievement->result }}@endif
                                    </p>
                                </div>
                            </div>
                        </td>
                        <td><x-admin.badge variant="info">{{ $achievement->type->label() }}</x-admin.badge></td>
                        <td>
                            <time datetime="{{ $achievement->achieved_at->toDateString() }}" class="whitespace-nowrap">
                                {{ $achievement->achieved_at->translatedFormat('M Y') }}
                            </time>
                        </td>
                        <td>
                            <div class="flex flex-wrap items-center gap-1.5">
                                @if ($achievement->media->isNotEmpty())
                                    <x-admin.badge variant="neutral">{{ $achievement->media->count() }} fotos</x-admin.badge>
                                @endif
                                @if ($achievement->certificate_path)
                                    <x-admin.badge variant="neutral">PDF</x-admin.badge>
                                @endif
                                @if ($achievement->media->isEmpty() && ! $achievement->certificate_path)
                                    <span class="text-xs text-slate-400">Sin adjuntos</span>
                                @endif
                            </div>
                        </td>
                        <td>
                            <x-admin.badge :variant="$achievement->is_visible ? 'success' : 'neutral'">
                                {{ $achievement->is_visible ? 'Visible' : 'Oculto' }}
                            </x-admin.badge>
                        </td>
                        <td>
                            <div class="admin-table__actions">
                                <x-admin.button size="sm" variant="ghost" wire:click="edit({{ $achievement->id }})">Editar</x-admin.button>
                                <x-admin.button size="sm" variant="danger-ghost" wire:click="confirmDelete({{ $achievement->id }})" aria-label="Eliminar {{ $achievement->title }}">Eliminar</x-admin.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <span class="admin-empty__icon"><flux:icon.trophy class="size-5" /></span>
                                <p class="admin-empty__title">Todavía no hay logros registrados</p>
                                <p class="admin-empty__copy">Añade una hackathon, certificación o reconocimiento para construir este archivo.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="grid gap-3 lg:hidden" aria-label="Listado de logros">
        @forelse ($achievements as $achievement)
            <article wire:key="achievement-card-{{ $achievement->id }}" class="admin-mobile-card">
                <div class="flex items-start gap-3">
                    <div class="admin-mobile-card__thumb">
                        @if ($achievement->imageUrl())
                            <img src="{{ $achievement->imageUrl() }}" alt="" class="size-full object-cover">
                        @else
                            <flux:icon.trophy class="size-5" />
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $achievement->title }}</h2>
                            @if ($achievement->is_featured)<span class="text-amber-500" aria-label="Logro destacado">★</span>@endif
                        </div>
                        <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $achievement->organization }}</p>
                    </div>
                    <x-admin.badge :variant="$achievement->is_visible ? 'success' : 'neutral'">{{ $achievement->is_visible ? 'Visible' : 'Oculto' }}</x-admin.badge>
                </div>
                <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $achievement->description ?: 'Sin descripción registrada.' }}</p>
                <div class="mt-3 flex flex-wrap items-center gap-2">
                    <x-admin.badge variant="info">{{ $achievement->type->label() }}</x-admin.badge>
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $achievement->achieved_at->translatedFormat('M Y') }}</span>
                    @if ($achievement->media->isNotEmpty())<span class="text-xs text-slate-500 dark:text-slate-400">· {{ $achievement->media->count() }} fotos</span>@endif
                    @if ($achievement->certificate_path)<span class="text-xs text-slate-500 dark:text-slate-400">· PDF</span>@endif
                </div>
                <div class="admin-mobile-card__footer">
                    <span>Orden {{ $achievement->sort_order }}</span>
                    <div class="flex items-center gap-1">
                        <x-admin.button size="sm" variant="ghost" wire:click="edit({{ $achievement->id }})">Editar</x-admin.button>
                        <x-admin.button size="sm" variant="danger-ghost" wire:click="confirmDelete({{ $achievement->id }})">Eliminar</x-admin.button>
                    </div>
                </div>
            </article>
        @empty
            <div class="admin-empty">
                <span class="admin-empty__icon"><flux:icon.trophy class="size-5" /></span>
                <p class="admin-empty__title">Todavía no hay logros registrados</p>
                <p class="admin-empty__copy">Añade una hackathon, certificación o reconocimiento para construir este archivo.</p>
            </div>
        @endforelse
    </section>

    <div class="admin-pagination">{{ $achievements->links() }}</div>

    @if ($showForm)
        <x-admin.form-modal
            name="achievement-form"
            model="showForm"
            :title="$editingAchievementId ? 'Editar logro' : 'Nuevo logro'"
            description="Registra el resultado, tu aporte y evidencias verificables."
            close-action="cancelForm"
            size="xl"
        >
            <form wire:submit="save" class="admin-form">
                <flux:input wire:model="title" label="Título" required autofocus />

                <div class="admin-form-grid">
                    <flux:select wire:model="type" label="Tipo">
                        @foreach ($types as $typeOption)
                            <flux:select.option value="{{ $typeOption->value }}">{{ $typeOption->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:input wire:model="achievedAt" label="Fecha" type="date" required />
                    <flux:input wire:model="organization" label="Evento u organización" required />
                    <flux:input wire:model="result" label="Resultado" placeholder="Ganador, finalista…" />
                    <flux:input wire:model="role" label="Tu rol" placeholder="Backend, liderazgo…" />
                    <flux:input wire:model="externalUrl" label="Enlace de evidencia" type="url" placeholder="https://" />
                </div>

                <flux:textarea wire:model="description" label="Descripción" rows="5" maxlength="3000" />

                <section class="admin-upload-panel" aria-label="Portada del logro">
                    <div class="admin-upload-panel__header">
                        <div>
                            <h3>Portada principal</h3>
                            <p>Imagen representativa del logro, máximo 3 MB.</p>
                        </div>
                        <flux:input wire:model="image" type="file" accept="image/jpeg,image/png,image/webp" aria-label="Seleccionar portada" />
                    </div>
                    <div wire:loading wire:target="image" class="mt-3 text-xs text-slate-500 dark:text-slate-400">Procesando imagen…</div>
                    @if ($image)
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Nueva imagen: {{ $image->getClientOriginalName() }}</p>
                    @elseif ($editingAchievement?->image_path && ! $removeCurrentImage)
                        <div class="mt-4 flex items-center gap-3">
                            <img src="{{ $editingAchievement->imageUrl() }}" alt="Imagen actual" class="h-20 w-32 rounded-lg object-cover ring-1 ring-slate-900/10 dark:ring-white/10">
                            <x-admin.button type="button" size="sm" variant="danger-ghost" wire:click="markImageForRemoval">Quitar portada</x-admin.button>
                        </div>
                    @elseif ($removeCurrentImage)
                        <p class="mt-3 text-xs font-medium text-amber-600 dark:text-amber-400">La imagen actual se eliminará al guardar.</p>
                    @endif
                </section>

                <x-admin.media-gallery-editor
                    id="achievement-media-gallery"
                    :media="$editingAchievement?->media ?? collect()"
                    :uploads="$galleryImages"
                    :limit="$galleryLimit"
                    title="Fotos del logro"
                    description="Guarda varias fotos de la hackathon, el equipo, la premiación o el certificado."
                    empty-text="Añade fotos que ayuden a contar qué ocurrió y cuál fue el resultado."
                />

                <section class="admin-upload-panel" aria-label="Certificado PDF">
                    <div class="admin-upload-panel__header">
                        <div>
                            <h3>Certificado PDF</h3>
                            <p>Documento verificable de máximo 5 MB.</p>
                        </div>
                        <flux:input wire:model="certificate" type="file" accept="application/pdf" aria-label="Seleccionar certificado PDF" />
                    </div>
                    <div wire:loading wire:target="certificate" class="mt-3 text-xs text-slate-500 dark:text-slate-400">Procesando documento…</div>
                    @if ($certificate)
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-400">Nuevo documento: {{ $certificate->getClientOriginalName() }}</p>
                    @elseif ($editingAchievement?->certificate_path && ! $removeCurrentCertificate)
                        <div class="mt-4 flex items-center justify-between gap-3">
                            <a href="{{ $editingAchievement->certificateUrl() }}" target="_blank" rel="noopener noreferrer" class="text-sm font-semibold text-sky-600 hover:text-sky-700 hover:underline dark:text-sky-400">Ver PDF actual</a>
                            <x-admin.button type="button" size="sm" variant="danger-ghost" wire:click="markCertificateForRemoval">Quitar PDF</x-admin.button>
                        </div>
                    @elseif ($removeCurrentCertificate)
                        <p class="mt-3 text-xs font-medium text-amber-600 dark:text-amber-400">El PDF actual se eliminará al guardar.</p>
                    @endif
                </section>

                <div class="admin-form-grid admin-form-grid--compact">
                    <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" max="65535" />
                    <div class="grid gap-3">
                        <label class="admin-check-card">
                            <input type="checkbox" wire:model="isFeatured">
                            <span><strong>Destacar primero</strong><small>Prioriza el logro en la sección pública.</small></span>
                        </label>
                        <label class="admin-check-card">
                            <input type="checkbox" wire:model="isVisible">
                            <span><strong>Visible en el portafolio</strong><small>Permite preparar contenido antes de publicarlo.</small></span>
                        </label>
                    </div>
                </div>

                <div class="admin-form__footer">
                    <x-admin.button type="button" variant="secondary" wire:click="cancelForm">Cancelar</x-admin.button>
                    <x-admin.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,image,certificate,galleryImages">
                        <flux:icon.check class="size-4" />
                        Guardar logro
                    </x-admin.button>
                </div>
            </form>
        </x-admin.form-modal>
    @endif

    <x-admin.form-modal
        name="delete-achievement"
        model="confirmingDelete"
        title="Eliminar logro"
        description="Esta acción no se puede deshacer."
        close-action="cancelDelete"
        size="sm"
    >
        <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">“{{ $deletingAchievementTitle }}” se eliminará junto con su portada, galería y certificado.</p>
        <div class="admin-form__footer mt-6">
            <x-admin.button type="button" variant="secondary" wire:click="cancelDelete">Cancelar</x-admin.button>
            <x-admin.button type="button" variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar logro</x-admin.button>
        </div>
    </x-admin.form-modal>

    <x-admin.form-modal
        name="delete-achievement-media"
        model="confirmingMediaDelete"
        title="Quitar foto"
        description="La foto también se eliminará del almacenamiento."
        close-action="cancelMediaDelete"
        size="sm"
    >
        <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">“{{ $deletingMediaName }}” se quitará de la galería.</p>
        <div class="admin-form__footer mt-6">
            <x-admin.button type="button" variant="secondary" wire:click="cancelMediaDelete">Cancelar</x-admin.button>
            <x-admin.button type="button" variant="danger" wire:click="deleteMedia" wire:loading.attr="disabled" wire:target="deleteMedia">Quitar foto</x-admin.button>
        </div>
    </x-admin.form-modal>
</div>
