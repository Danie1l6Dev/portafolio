<div class="admin-page">
    <x-admin.page-header
        title="Categorías"
        description="Organiza los proyectos para que el portafolio sea fácil de explorar."
        :count="$categories->total()"
    >
        <x-slot:actions>
            <x-admin.button variant="primary" size="sm" wire:click="create">
                <flux:icon.plus class="size-4" />
                Nueva categoría
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="space-y-4" aria-label="Listado de categorías">
        <div class="admin-toolbar">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Buscar por nombre o descripción"
                aria-label="Buscar categorías"
            />
        </div>

        <div wire:loading.flex wire:target="search,save,delete" class="admin-loading">
            <flux:icon.loading class="size-4" />
            Actualizando categorías…
        </div>

        <div class="admin-table-shell hidden md:block">
            <div class="overflow-x-auto">
                <table class="admin-table min-w-[42rem]">
                    <thead>
                        <tr>
                            <th>Categoría</th>
                            <th>Color</th>
                            <th>Proyectos</th>
                            <th>Orden</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr wire:key="category-table-{{ $category->id }}">
                                <td>
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $category->name }}</p>
                                    <p class="mt-0.5 max-w-md truncate text-xs text-slate-400 dark:text-slate-500">{{ $category->description ?: 'Sin descripción' }}</p>
                                </td>
                                <td>
                                    <span class="inline-flex items-center gap-2">
                                        <span class="size-3 rounded-full ring-1 ring-black/10 dark:ring-white/15" style="background-color: {{ $category->color ?: '#94a3b8' }}"></span>
                                        <span class="font-mono text-xs text-slate-500 dark:text-slate-400">{{ $category->color ?: '—' }}</span>
                                    </span>
                                </td>
                                <td class="tabular-nums text-slate-600 dark:text-slate-300">{{ $category->projects_count }}</td>
                                <td class="tabular-nums text-slate-500 dark:text-slate-400">{{ $category->sort_order }}</td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-admin.button variant="secondary" size="sm" wire:click="edit({{ $category->id }})">Editar</x-admin.button>
                                        <x-admin.button variant="danger" size="sm" wire:click="confirmDelete({{ $category->id }})">Eliminar</x-admin.button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="admin-empty">
                                        <strong class="block text-sm font-semibold text-slate-700 dark:text-slate-200">No hay categorías aquí</strong>
                                        <span class="mt-1 block text-xs">Prueba otra búsqueda o crea la primera categoría.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-3 md:hidden">
            @forelse ($categories as $category)
                <article wire:key="category-card-{{ $category->id }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start justify-between gap-4">
                        <div class="min-w-0">
                            <div class="flex items-center gap-2">
                                <span class="size-3 shrink-0 rounded-full ring-1 ring-black/10 dark:ring-white/15" style="background-color: {{ $category->color ?: '#94a3b8' }}"></span>
                                <h2 class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $category->name }}</h2>
                            </div>
                            <p class="mt-2 line-clamp-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $category->description ?: 'Sin descripción' }}</p>
                        </div>
                        <span class="shrink-0 text-xs tabular-nums text-slate-400">Orden {{ $category->sort_order }}</span>
                    </div>

                    <div class="mt-4 flex items-center justify-between gap-3 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <span class="text-xs text-slate-500 dark:text-slate-400">{{ $category->projects_count }} {{ $category->projects_count === 1 ? 'proyecto' : 'proyectos' }}</span>
                        <div class="flex gap-2">
                            <x-admin.button variant="secondary" size="sm" wire:click="edit({{ $category->id }})">Editar</x-admin.button>
                            <x-admin.button variant="danger" size="sm" wire:click="confirmDelete({{ $category->id }})" aria-label="Eliminar {{ $category->name }}">
                                <flux:icon.trash class="size-4" />
                            </x-admin.button>
                        </div>
                    </div>
                </article>
            @empty
                <div class="admin-empty rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                    <strong class="block text-sm font-semibold text-slate-700 dark:text-slate-200">No hay categorías aquí</strong>
                    <span class="mt-1 block text-xs">Prueba otra búsqueda o crea la primera categoría.</span>
                </div>
            @endforelse
        </div>

        <div class="admin-pagination">{{ $categories->links() }}</div>
    </section>

    <x-admin.form-modal
        name="category-form"
        model="showForm"
        :title="$editingCategoryId ? 'Editar categoría' : 'Nueva categoría'"
        description="Define el nombre, el color de referencia y su posición en los filtros."
        close-action="cancelForm"
        size="md"
    >
        <form wire:submit="save" class="admin-form">
            <flux:input wire:model="name" label="Nombre" required autofocus />
            <flux:textarea wire:model="description" label="Descripción" rows="4" />

            <div class="admin-form-grid">
                <flux:input wire:model="color" label="Color hexadecimal" placeholder="#0284C7" />
                <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" />
            </div>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                <x-admin.button type="button" variant="secondary" wire:click="cancelForm">Cancelar</x-admin.button>
                <x-admin.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">Guardar categoría</x-admin.button>
            </div>
        </form>
    </x-admin.form-modal>

    <x-admin.form-modal
        name="delete-category"
        model="confirmingDelete"
        title="Eliminar categoría"
        :description="'“'.$deletingCategoryName.'” desaparecerá de los filtros. Esta acción no se puede deshacer.'"
        close-action="cancelDelete"
        size="sm"
    >
        <div class="space-y-5">
            @error('delete')
                <flux:callout variant="danger" icon="exclamation-triangle" heading="No se puede eliminar">{{ $message }}</flux:callout>
            @enderror

            <div class="flex justify-end gap-3">
                <x-admin.button variant="secondary" wire:click="cancelDelete">Cancelar</x-admin.button>
                <x-admin.button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</x-admin.button>
            </div>
        </div>
    </x-admin.form-modal>
</div>
