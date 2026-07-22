<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><div class="flex items-center gap-3"><flux:heading size="xl" level="1">Proyectos</flux:heading><span class="rounded-full bg-zinc-100 px-2.5 py-1 text-xs font-semibold tabular-nums text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ $projects->total() }}</span></div><flux:text variant="subtle">Tu vitrina editorial: del borrador a la portada, con tecnología y galería.</flux:text></div>
        <flux:button variant="primary" icon="plus" wire:click="create">Nuevo proyecto</flux:button>
    </header>

    <div class="grid gap-6 {{ $showForm ? 'xl:grid-cols-[minmax(0,1fr)_32rem]' : '' }}">
        <section class="min-w-0 space-y-4" aria-label="Listado de proyectos">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_11rem_13rem]">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar título o resumen" aria-label="Buscar proyectos" />
                <flux:select wire:model.live="statusFilter" aria-label="Filtrar por estado"><flux:select.option value="">Todos los estados</flux:select.option><flux:select.option value="published">Publicados</flux:select.option><flux:select.option value="draft">Borradores</flux:select.option><flux:select.option value="archived">Archivados</flux:select.option></flux:select>
                <flux:select wire:model.live="categoryFilter" aria-label="Filtrar por categoría"><flux:select.option value="">Todas las categorías</flux:select.option>@foreach($categories as $category)<flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>@endforeach</flux:select>
            </div>

            <div wire:loading.flex wire:target="search,statusFilter,categoryFilter,save,delete" class="items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900"><flux:icon.loading class="size-4" /> Actualizando vitrina…</div>

            <div class="grid gap-3">
                @forelse ($projects as $project)
                    <article wire:key="project-{{ $project->id }}" class="group overflow-hidden rounded-xl border border-zinc-200/80 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                        <div class="grid sm:grid-cols-[8.5rem_minmax(0,1fr)]">
                            <div class="relative min-h-32 bg-zinc-100 dark:bg-zinc-800">
                                @if ($project->cover_image)<img class="absolute inset-0 size-full object-cover" src="{{ asset('storage/'.ltrim($project->cover_image, '/')) }}" alt="Portada de {{ $project->title }}" />@else<div class="absolute inset-0 grid place-items-center"><span class="text-xs font-medium uppercase tracking-widest text-zinc-400">Sin portada</span></div>@endif
                                <span class="absolute left-3 top-3 rounded-full px-2 py-1 text-[10px] font-semibold uppercase tracking-wider shadow-sm {{ $project->status === 'published' ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300' : ($project->status === 'draft' ? 'bg-amber-50 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300' : 'bg-zinc-200 text-zinc-700 dark:bg-zinc-700 dark:text-zinc-200') }}">{{ $project->status === 'published' ? 'Publicado' : ($project->status === 'draft' ? 'Borrador' : 'Archivado') }}</span>
                            </div>
                            <div class="flex min-w-0 flex-col justify-between gap-4 p-4 sm:flex-row sm:items-center">
                                <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><flux:heading size="lg" class="truncate">{{ $project->title }}</flux:heading>@if($project->is_featured)<span class="rounded-full bg-violet-50 px-2 py-0.5 text-[11px] font-semibold text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">Destacado</span>@endif</div><p class="mt-1 line-clamp-2 text-sm leading-6 text-zinc-500">{{ $project->summary }}</p><div class="mt-3 flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-zinc-400"><span>{{ $project->category?->name ?? 'Sin categoría' }}</span><span>{{ $project->skills->count() }} habilidades</span><span>Orden {{ $project->sort_order }}</span></div></div>
                                <div class="flex shrink-0 items-center gap-1 self-end sm:self-auto"><flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $project->id }})">Editar</flux:button><flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $project->id }})" aria-label="Eliminar {{ $project->title }}" /></div>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-xl border border-dashed border-zinc-300 px-6 py-14 text-center dark:border-zinc-700"><div class="mx-auto grid size-12 place-items-center rounded-full bg-zinc-100 dark:bg-zinc-800"><flux:icon.folder-plus class="size-5 text-zinc-400" /></div><flux:heading size="lg" class="mt-3">La vitrina está vacía</flux:heading><flux:text variant="subtle" class="mt-1">Cambia los filtros o prepara tu primer proyecto.</flux:text></div>
                @endforelse
            </div>
            {{ $projects->links() }}
        </section>

        @if ($showForm)
            <aside class="h-fit rounded-xl border border-zinc-200/80 bg-zinc-50/70 dark:border-zinc-700 dark:bg-zinc-900" aria-label="Formulario de proyecto">
                <div class="border-b border-zinc-200 p-5 dark:border-zinc-700"><flux:heading size="lg">{{ $editingProjectId ? 'Editar proyecto' : 'Nuevo proyecto' }}</flux:heading><flux:text variant="subtle" class="text-sm">Los campos públicos definen la tarjeta; la descripción vive en el detalle.</flux:text></div>
                <form wire:submit="save" class="space-y-5 p-5">
                    <flux:input wire:model="title" label="Título" required autofocus />
                    <div class="grid grid-cols-2 gap-4"><flux:select wire:model="categoryId" label="Categoría"><flux:select.option value="">Sin categoría</flux:select.option>@foreach($categories as $category)<flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>@endforeach</flux:select><flux:select wire:model="status" label="Estado"><flux:select.option value="draft">Borrador</flux:select.option><flux:select.option value="published">Publicado</flux:select.option><flux:select.option value="archived">Archivado</flux:select.option></flux:select></div>
                    <flux:textarea wire:model="summary" label="Resumen de tarjeta" rows="3" maxlength="500" />
                    <flux:textarea wire:model="description" label="Descripción completa" rows="7" />
                    <div class="grid gap-4 sm:grid-cols-2"><flux:input wire:model="demoUrl" label="Demo" type="url" placeholder="https://" /><flux:input wire:model="repoUrl" label="Repositorio" type="url" placeholder="https://" /></div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <flux:input wire:model="startedAt" label="Inicio" type="date" />
                        <flux:input wire:model="finishedAt" label="Fin" type="date" />
                        <div class="sm:col-span-2">
                            <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" />
                        </div>
                    </div>
                    <label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-3 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><input type="checkbox" wire:model="isFeatured" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600" /><span><strong class="block font-medium">Proyecto destacado</strong><span class="text-xs text-zinc-500">Aparecerá en el carrusel de proyectos de la portada cuando esté publicado.</span></span></label>

                    <fieldset class="space-y-2">
                        <legend class="text-sm font-medium">Habilidades vinculadas</legend>
                        <div class="grid max-h-52 grid-cols-2 gap-2 overflow-y-auto rounded-lg border border-zinc-200 bg-white p-3 dark:border-zinc-700 dark:bg-zinc-950">
                            @forelse($skills as $skill)
                                <label wire:key="project-skill-{{ $skill->id }}" class="flex min-w-0 cursor-pointer items-center gap-2 rounded-lg border border-transparent px-2 py-2 text-sm hover:border-zinc-200 hover:bg-zinc-50 dark:hover:border-zinc-700 dark:hover:bg-zinc-800">
                                    <input type="checkbox" wire:model="skillIds" value="{{ $skill->id }}" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600" />
                                    <x-portfolio.skill-icon :icon="$skill->icon" :name="$skill->name" size="sm" />
                                    <span class="truncate">{{ $skill->name }}</span>
                                </label>
                            @empty
                                <p class="col-span-2 text-xs text-zinc-500">Primero crea habilidades.</p>
                            @endforelse
                        </div>
                    </fieldset>

                    <div class="space-y-3"><flux:input wire:model="coverImage" label="Portada" type="file" accept="image/jpeg,image/png,image/webp" />@if($coverImage instanceof \Livewire\Features\SupportFileUploads\TemporaryUploadedFile && $coverImage->isPreviewable())<img class="h-28 w-full rounded-lg object-cover ring-1 ring-black/10" src="{{ $coverImage->temporaryUrl() }}" alt="Vista previa de la nueva portada" />@elseif($editingProject?->cover_image)<img class="h-28 w-full rounded-lg object-cover ring-1 ring-black/10" src="{{ asset('storage/'.ltrim($editingProject->cover_image, '/')) }}" alt="Portada actual" />@endif<div wire:loading wire:target="coverImage" class="text-xs text-zinc-500">Preparando portada…</div></div>

                    <x-admin.media-gallery-editor
                        id="project-media-gallery"
                        :media="$editingProject?->media ?? collect()"
                        :uploads="$galleryImages"
                        :limit="$galleryLimit"
                        title="Galería del proyecto"
                        description="Carga varias capturas, amplíalas, ordénalas arrastrando y elige cualquiera como portada."
                        empty-text="Añade capturas que expliquen el producto y sus estados principales."
                    />

                    <div class="flex justify-end gap-2 border-t border-zinc-200 pt-5 dark:border-zinc-700"><flux:button type="button" variant="ghost" wire:click="cancelForm">Cancelar</flux:button><flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,coverImage,galleryImages">Guardar proyecto</flux:button></div>
                </form>
            </aside>
        @endif
    </div>

    <flux:modal name="delete-project" wire:model="confirmingDelete" class="max-w-md"><div class="space-y-5"><div><flux:heading size="lg">Eliminar proyecto</flux:heading><flux:text variant="subtle" class="mt-1">“{{ $deletingProjectTitle }}”, su portada y su galería se eliminarán definitivamente.</flux:text></div><div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</flux:button></div></div></flux:modal>
    <flux:modal name="delete-project-media" wire:model="confirmingMediaDelete" class="max-w-md"><div class="space-y-5"><div><flux:heading size="lg">Quitar imagen</flux:heading><flux:text variant="subtle" class="mt-1">“{{ $deletingMediaName }}” se eliminará de la galería y del almacenamiento.</flux:text></div><div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelMediaDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="deleteMedia" wire:loading.attr="disabled" wire:target="deleteMedia">Quitar imagen</flux:button></div></div></flux:modal>
</div>
