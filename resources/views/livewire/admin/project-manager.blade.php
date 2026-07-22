<div class="admin-page">
    <x-admin.page-header
        title="Proyectos"
        description="Organiza la vitrina, sus estados, tecnologías y archivos visuales."
        :count="$projects->total()"
    >
        <x-slot:actions>
            <x-admin.button variant="primary" wire:click="create">
                <flux:icon.plus class="size-4" />
                Nuevo proyecto
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-toolbar" aria-label="Filtros de proyectos">
        <div class="min-w-0 flex-1 lg:max-w-md">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Buscar título o resumen"
                aria-label="Buscar proyectos"
            />
        </div>

        <div class="admin-filter-chips" role="group" aria-label="Filtrar por estado">
            @foreach (['' => 'Todos', 'published' => 'Publicados', 'draft' => 'Borradores', 'archived' => 'Archivados'] as $value => $label)
                <button
                    type="button"
                    wire:click="$set('statusFilter', '{{ $value }}')"
                    class="admin-filter-chip {{ $statusFilter === $value ? 'is-active' : '' }}"
                    aria-pressed="{{ $statusFilter === $value ? 'true' : 'false' }}"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        <div class="w-full sm:w-56">
            <flux:select wire:model.live="categoryFilter" aria-label="Filtrar por categoría">
                <flux:select.option value="">Todas las categorías</flux:select.option>
                @foreach ($categories as $category)
                    <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                @endforeach
            </flux:select>
        </div>
    </section>

    <div wire:loading.flex wire:target="search,statusFilter,categoryFilter,save,delete" class="admin-loading">
        <flux:icon.loading class="size-4" />
        Actualizando catálogo…
    </div>

    <section class="admin-table-shell hidden lg:block" aria-label="Listado de proyectos">
        <table class="admin-table">
            <thead>
                <tr>
                    <th scope="col">Proyecto</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Portada</th>
                    <th scope="col">Orden</th>
                    <th scope="col"><span class="sr-only">Acciones</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($projects as $project)
                    @php
                        $statusLabel = match ($project->status) {
                            'published' => 'Publicado',
                            'draft' => 'Borrador',
                            default => 'Archivado',
                        };
                        $statusVariant = match ($project->status) {
                            'published' => 'success',
                            'draft' => 'warning',
                            default => 'neutral',
                        };
                    @endphp
                    <tr wire:key="project-row-{{ $project->id }}">
                        <td>
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="admin-table__thumb">
                                    @if ($project->cover_image)
                                        <img src="{{ asset('storage/'.ltrim($project->cover_image, '/')) }}" alt="" class="size-full object-cover">
                                    @else
                                        <flux:icon.photo class="size-4" />
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <div class="flex items-center gap-2">
                                        <p class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ $project->title }}</p>
                                        @if ($project->is_featured)
                                            <span class="text-amber-500" title="Proyecto destacado" aria-label="Proyecto destacado">★</span>
                                        @endif
                                    </div>
                                    <p class="mt-0.5 max-w-sm truncate text-xs text-slate-500 dark:text-slate-400">{{ $project->slug }}</p>
                                </div>
                            </div>
                        </td>
                        <td>{{ $project->category?->name ?? 'Sin categoría' }}</td>
                        <td><x-admin.badge :variant="$statusVariant">{{ $statusLabel }}</x-admin.badge></td>
                        <td>
                            <span class="text-xs text-slate-500 dark:text-slate-400">
                                {{ $project->skills->count() }} {{ $project->skills->count() === 1 ? 'tecnología' : 'tecnologías' }}
                            </span>
                        </td>
                        <td class="tabular-nums">{{ $project->sort_order }}</td>
                        <td>
                            <div class="admin-table__actions">
                                <x-admin.button size="sm" variant="ghost" wire:click="edit({{ $project->id }})">Editar</x-admin.button>
                                <x-admin.button size="sm" variant="danger-ghost" wire:click="confirmDelete({{ $project->id }})" aria-label="Eliminar {{ $project->title }}">Eliminar</x-admin.button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="admin-empty">
                                <span class="admin-empty__icon"><flux:icon.folder-plus class="size-5" /></span>
                                <p class="admin-empty__title">No hay proyectos para mostrar</p>
                                <p class="admin-empty__copy">Cambia los filtros o registra el primer proyecto del catálogo.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </section>

    <section class="grid gap-3 lg:hidden" aria-label="Listado de proyectos">
        @forelse ($projects as $project)
            @php
                $statusLabel = match ($project->status) {
                    'published' => 'Publicado',
                    'draft' => 'Borrador',
                    default => 'Archivado',
                };
                $statusVariant = match ($project->status) {
                    'published' => 'success',
                    'draft' => 'warning',
                    default => 'neutral',
                };
            @endphp
            <article wire:key="project-card-{{ $project->id }}" class="admin-mobile-card">
                <div class="flex items-start gap-3">
                    <div class="admin-mobile-card__thumb">
                        @if ($project->cover_image)
                            <img src="{{ asset('storage/'.ltrim($project->cover_image, '/')) }}" alt="" class="size-full object-cover">
                        @else
                            <flux:icon.photo class="size-5" />
                        @endif
                    </div>
                    <div class="min-w-0 flex-1">
                        <div class="flex flex-wrap items-center gap-2">
                            <h2 class="truncate text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $project->title }}</h2>
                            @if ($project->is_featured)<span class="text-amber-500" aria-label="Proyecto destacado">★</span>@endif
                        </div>
                        <p class="mt-1 truncate text-xs text-slate-500 dark:text-slate-400">{{ $project->category?->name ?? 'Sin categoría' }}</p>
                    </div>
                    <x-admin.badge :variant="$statusVariant">{{ $statusLabel }}</x-admin.badge>
                </div>
                <p class="mt-3 line-clamp-2 text-sm leading-6 text-slate-600 dark:text-slate-300">{{ $project->summary }}</p>
                <div class="admin-mobile-card__footer">
                    <span>{{ $project->skills->count() }} tecnologías · Orden {{ $project->sort_order }}</span>
                    <div class="flex items-center gap-1">
                        <x-admin.button size="sm" variant="ghost" wire:click="edit({{ $project->id }})">Editar</x-admin.button>
                        <x-admin.button size="sm" variant="danger-ghost" wire:click="confirmDelete({{ $project->id }})" aria-label="Eliminar {{ $project->title }}">Eliminar</x-admin.button>
                    </div>
                </div>
            </article>
        @empty
            <div class="admin-empty">
                <span class="admin-empty__icon"><flux:icon.folder-plus class="size-5" /></span>
                <p class="admin-empty__title">No hay proyectos para mostrar</p>
                <p class="admin-empty__copy">Cambia los filtros o registra el primer proyecto del catálogo.</p>
            </div>
        @endforelse
    </section>

    <div class="admin-pagination">{{ $projects->links() }}</div>

    @if ($showForm)
        <x-admin.form-modal
            name="project-form"
            model="showForm"
            :title="$editingProjectId ? 'Editar proyecto' : 'Nuevo proyecto'"
            description="Define la ficha pública, su visibilidad y el archivo visual del proyecto."
            close-action="cancelForm"
            size="xl"
        >
            <form wire:submit="save" class="admin-form">
                <flux:input wire:model="title" label="Título" required autofocus />

                <div class="admin-form-grid">
                    <flux:select wire:model="categoryId" label="Categoría">
                        <flux:select.option value="">Sin categoría</flux:select.option>
                        @foreach ($categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                    <flux:select wire:model="status" label="Estado">
                        <flux:select.option value="draft">Borrador</flux:select.option>
                        <flux:select.option value="published">Publicado</flux:select.option>
                        <flux:select.option value="archived">Archivado</flux:select.option>
                    </flux:select>
                </div>

                <flux:textarea wire:model="summary" label="Resumen de tarjeta" rows="3" maxlength="500" />
                <flux:textarea wire:model="description" label="Descripción completa" rows="7" />

                <div class="admin-form-grid">
                    <flux:input wire:model="demoUrl" label="Demo" type="url" placeholder="https://" />
                    <flux:input wire:model="repoUrl" label="Repositorio" type="url" placeholder="https://" />
                    <flux:input wire:model="startedAt" label="Inicio" type="date" />
                    <flux:input wire:model="finishedAt" label="Fin" type="date" />
                </div>

                <div class="admin-form-grid admin-form-grid--compact">
                    <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" />
                    <label class="admin-check-card">
                        <input type="checkbox" wire:model="isFeatured">
                        <span>
                            <strong>Proyecto destacado</strong>
                            <small>Aparecerá en el carrusel cuando esté publicado.</small>
                        </span>
                    </label>
                </div>

                <fieldset class="admin-fieldset">
                    <div class="admin-fieldset__heading">
                        <legend>Habilidades vinculadas</legend>
                        <span>{{ count($skillIds) }} seleccionadas</span>
                    </div>
                    <div class="grid max-h-56 gap-2 overflow-y-auto p-3 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($skills as $skill)
                            <label wire:key="project-skill-{{ $skill->id }}" class="admin-skill-option">
                                <input type="checkbox" wire:model="skillIds" value="{{ $skill->id }}">
                                <x-portfolio.skill-icon :icon="$skill->icon" :name="$skill->name" size="sm" />
                                <span class="truncate">{{ $skill->name }}</span>
                            </label>
                        @empty
                            <p class="text-xs text-slate-500 dark:text-slate-400 sm:col-span-2 lg:col-span-3">Primero crea habilidades para vincularlas.</p>
                        @endforelse
                    </div>
                </fieldset>

                <section class="admin-upload-panel" aria-label="Portada del proyecto">
                    <div class="admin-upload-panel__header">
                        <div>
                            <h3>Portada principal</h3>
                            <p>JPG, PNG o WebP de máximo 2 MB.</p>
                        </div>
                        <flux:input wire:model="coverImage" type="file" accept="image/jpeg,image/png,image/webp" aria-label="Seleccionar portada" />
                    </div>
                    <div wire:loading wire:target="coverImage" class="mt-3 text-xs text-slate-500 dark:text-slate-400">Preparando portada…</div>
                    @if ($coverImage instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $coverImage->isPreviewable())
                        <img class="mt-4 aspect-[16/6] w-full rounded-lg object-cover ring-1 ring-slate-900/10 dark:ring-white/10" src="{{ $coverImage->temporaryUrl() }}" alt="Vista previa de la nueva portada">
                    @elseif ($editingProject?->cover_image)
                        <img class="mt-4 aspect-[16/6] w-full rounded-lg object-cover ring-1 ring-slate-900/10 dark:ring-white/10" src="{{ asset('storage/'.ltrim($editingProject->cover_image, '/')) }}" alt="Portada actual">
                    @endif
                </section>

                <x-admin.media-gallery-editor
                    id="project-media-gallery"
                    :media="$editingProject?->media ?? collect()"
                    :uploads="$galleryImages"
                    :limit="$galleryLimit"
                    title="Galería del proyecto"
                    description="Carga varias capturas, amplíalas, ordénalas y elige cualquiera como portada."
                    empty-text="Añade capturas que expliquen el producto y sus estados principales."
                />

                <div class="admin-form__footer">
                    <x-admin.button type="button" variant="secondary" wire:click="cancelForm">Cancelar</x-admin.button>
                    <x-admin.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,coverImage,galleryImages">
                        <flux:icon.check class="size-4" />
                        Guardar proyecto
                    </x-admin.button>
                </div>
            </form>
        </x-admin.form-modal>
    @endif

    <x-admin.form-modal
        name="delete-project"
        model="confirmingDelete"
        title="Eliminar proyecto"
        description="Esta acción no se puede deshacer."
        close-action="cancelDelete"
        size="sm"
    >
        <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">“{{ $deletingProjectTitle }}”, su portada y su galería se eliminarán definitivamente.</p>
        <div class="admin-form__footer mt-6">
            <x-admin.button type="button" variant="secondary" wire:click="cancelDelete">Cancelar</x-admin.button>
            <x-admin.button type="button" variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar proyecto</x-admin.button>
        </div>
    </x-admin.form-modal>

    <x-admin.form-modal
        name="delete-project-media"
        model="confirmingMediaDelete"
        title="Quitar imagen"
        description="La imagen también se eliminará del almacenamiento."
        close-action="cancelMediaDelete"
        size="sm"
    >
        <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">“{{ $deletingMediaName }}” se quitará de la galería.</p>
        <div class="admin-form__footer mt-6">
            <x-admin.button type="button" variant="secondary" wire:click="cancelMediaDelete">Cancelar</x-admin.button>
            <x-admin.button type="button" variant="danger" wire:click="deleteMedia" wire:loading.attr="disabled" wire:target="deleteMedia">Quitar imagen</x-admin.button>
        </div>
    </x-admin.form-modal>
</div>
