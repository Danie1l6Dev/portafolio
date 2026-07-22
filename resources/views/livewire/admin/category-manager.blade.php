<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><flux:heading size="xl" level="1">Categorías</flux:heading><flux:text variant="subtle">Ordena los caminos por los que se explora tu trabajo.</flux:text></div>
        <flux:button variant="primary" icon="plus" wire:click="create">Nueva categoría</flux:button>
    </header>

    <div class="grid gap-6 {{ $showForm ? 'xl:grid-cols-[minmax(0,1fr)_24rem]' : '' }}">
        <section class="min-w-0 space-y-4" aria-label="Listado de categorías">
            <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar por nombre o descripción" aria-label="Buscar categorías" />

            <div class="overflow-hidden rounded-xl border border-zinc-200/80 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div wire:loading.flex wire:target="search,save,delete" class="items-center gap-2 border-b border-zinc-200 px-4 py-3 text-sm text-zinc-500 dark:border-zinc-700"><flux:icon.loading class="size-4" /> Actualizando categorías…</div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[42rem] text-left text-sm">
                        <thead class="bg-zinc-50 text-xs uppercase tracking-wider text-zinc-500 dark:bg-zinc-800/70"><tr><th class="px-4 py-3 font-medium">Categoría</th><th class="px-4 py-3 font-medium">Color</th><th class="px-4 py-3 font-medium">Proyectos</th><th class="px-4 py-3 font-medium">Orden</th><th class="px-4 py-3 text-right font-medium">Acciones</th></tr></thead>
                        <tbody class="divide-y divide-zinc-200/70 dark:divide-zinc-700">
                            @forelse ($categories as $category)
                                <tr wire:key="category-{{ $category->id }}" class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                                    <td class="px-4 py-3"><p class="font-medium text-zinc-950 dark:text-white">{{ $category->name }}</p><p class="max-w-md truncate text-xs text-zinc-500">{{ $category->description ?: 'Sin descripción' }}</p></td>
                                    <td class="px-4 py-3"><span class="inline-flex items-center gap-2"><span class="size-3 rounded-full ring-1 ring-black/10" style="background-color: {{ $category->color ?: '#a1a1aa' }}"></span><span class="font-mono text-xs text-zinc-500">{{ $category->color ?: '—' }}</span></span></td>
                                    <td class="px-4 py-3 tabular-nums">{{ $category->projects_count }}</td>
                                    <td class="px-4 py-3 tabular-nums text-zinc-500">{{ $category->sort_order }}</td>
                                    <td class="px-4 py-3"><div class="flex justify-end gap-1"><flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $category->id }})" aria-label="Editar {{ $category->name }}" /><flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $category->id }})" aria-label="Eliminar {{ $category->name }}" /></div></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="px-6 py-12 text-center"><flux:heading size="lg">No hay categorías aquí</flux:heading><flux:text variant="subtle" class="mt-1">Prueba otra búsqueda o crea la primera categoría.</flux:text></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $categories->links() }}
        </section>

        @if ($showForm)
            <aside class="h-fit rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-900" aria-label="Formulario de categoría">
                <div class="mb-5"><flux:heading size="lg">{{ $editingCategoryId ? 'Editar categoría' : 'Nueva categoría' }}</flux:heading><flux:text variant="subtle" class="text-sm">Nombre, contexto visual y posición en filtros.</flux:text></div>
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="name" label="Nombre" required autofocus />
                    <flux:textarea wire:model="description" label="Descripción" rows="4" />
                    <div class="grid grid-cols-2 gap-4"><flux:input wire:model="color" label="Color hexadecimal" placeholder="#3B82F6" /><flux:input wire:model="sortOrder" label="Orden" type="number" min="0" /></div>
                    <div class="flex justify-end gap-2 pt-2"><flux:button type="button" variant="ghost" wire:click="cancelForm">Cancelar</flux:button><flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">Guardar</flux:button></div>
                </form>
            </aside>
        @endif
    </div>

    <flux:modal name="delete-category" wire:model="confirmingDelete" class="max-w-md">
        <div class="space-y-5"><div><flux:heading size="lg">Eliminar categoría</flux:heading><flux:text variant="subtle" class="mt-1">“{{ $deletingCategoryName }}” desaparecerá de los filtros. Esta acción no se puede deshacer.</flux:text></div>@error('delete')<flux:callout variant="danger" icon="exclamation-triangle" heading="No se puede eliminar">{{ $message }}</flux:callout>@enderror<div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</flux:button></div></div>
    </flux:modal>
</div>
