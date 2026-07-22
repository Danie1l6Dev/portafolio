<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><flux:heading size="xl" level="1">Mapa de habilidades</flux:heading><flux:text variant="subtle">Organiza las tecnologías por grupo, uso en proyectos y relevancia.</flux:text></div>
        <flux:button variant="primary" icon="plus" wire:click="create">Nueva habilidad</flux:button>
    </header>

    <div class="grid gap-6 {{ $showForm ? 'xl:grid-cols-[minmax(0,1fr)_24rem]' : '' }}">
        <section class="min-w-0 space-y-4" aria-label="Listado de habilidades">
            <div class="grid gap-3 md:grid-cols-[minmax(0,1fr)_13rem_auto]">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar habilidad" aria-label="Buscar habilidades" />
                <flux:select wire:model.live="groupFilter" aria-label="Filtrar por grupo"><flux:select.option value="">Todos los grupos</flux:select.option>@foreach ($groups as $groupName)<flux:select.option value="{{ $groupName }}">{{ $groupName }}</flux:select.option>@endforeach</flux:select>
                <label class="flex min-h-10 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900"><input type="checkbox" wire:model.live="featuredOnly" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600" /><span>Solo destacadas</span></label>
            </div>

            <div class="overflow-hidden rounded-xl border border-zinc-200/80 bg-white dark:border-zinc-700 dark:bg-zinc-900">
                <div wire:loading.flex wire:target="search,groupFilter,featuredOnly,save,delete" class="items-center gap-2 border-b border-zinc-200 px-4 py-3 text-sm text-zinc-500 dark:border-zinc-700"><flux:icon.loading class="size-4" /> Reordenando el mapa…</div>
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[38rem] text-left text-sm">
                        <thead class="bg-zinc-50 text-xs uppercase tracking-wider text-zinc-500 dark:bg-zinc-800/70"><tr><th class="px-4 py-3 font-medium">Habilidad</th><th class="px-4 py-3 font-medium">Grupo</th><th class="px-4 py-3 font-medium">Uso</th><th class="px-4 py-3 text-right font-medium">Acciones</th></tr></thead>
                        <tbody class="divide-y divide-zinc-200/70 dark:divide-zinc-700">
                            @forelse ($skills as $skill)
                                <tr wire:key="skill-{{ $skill->id }}" class="hover:bg-zinc-50/70 dark:hover:bg-zinc-800/40">
                                    <td class="px-4 py-3"><div class="flex items-center gap-3"><span class="grid size-9 place-items-center rounded-lg bg-zinc-100 text-xs font-semibold text-zinc-600 dark:bg-zinc-800 dark:text-zinc-300">{{ str($skill->name)->substr(0, 2)->upper() }}</span><div><p class="font-medium text-zinc-950 dark:text-white">{{ $skill->name }}</p><p class="font-mono text-xs text-zinc-400">{{ $skill->icon ?: 'sin icono' }}</p></div>@if($skill->is_featured)<span class="rounded-full bg-violet-50 px-2 py-0.5 text-[11px] font-semibold text-violet-700 dark:bg-violet-400/10 dark:text-violet-300">Destacada</span>@endif</div></td>
                                    <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300">{{ $skill->group ?: 'Sin grupo' }}</td>
                                    <td class="px-4 py-3 tabular-nums text-zinc-500">{{ $skill->projects_count }} proyectos</td>
                                    <td class="px-4 py-3"><div class="flex justify-end gap-1"><flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $skill->id }})" aria-label="Editar {{ $skill->name }}" /><flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $skill->id }})" aria-label="Eliminar {{ $skill->name }}" /></div></td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-12 text-center"><flux:heading size="lg">Sin coincidencias técnicas</flux:heading><flux:text variant="subtle" class="mt-1">Ajusta los filtros o registra una habilidad.</flux:text></td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $skills->links() }}
        </section>

        @if ($showForm)
            <aside class="h-fit rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-900" aria-label="Formulario de habilidad">
                <div class="mb-5"><flux:heading size="lg">{{ $editingSkillId ? 'Editar habilidad' : 'Nueva habilidad' }}</flux:heading><flux:text variant="subtle" class="text-sm">Define su grupo, icono y posición en el portafolio.</flux:text></div>
                <form wire:submit="save" class="space-y-4">
                    <flux:input wire:model="name" label="Nombre" required autofocus />
                    <flux:select wire:model="group" label="Grupo"><flux:select.option value="">Sin grupo</flux:select.option>@foreach ($groups as $groupName)<flux:select.option value="{{ $groupName }}">{{ $groupName }}</flux:select.option>@endforeach</flux:select>
                    <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" />
                    <flux:input wire:model="icon" label="Icono" placeholder="si:laravel" />
                    <label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-3 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><input type="checkbox" wire:model="isFeatured" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600" /><span><strong class="block font-medium">Destacar habilidad</strong><span class="text-xs text-zinc-500">Aparecerá en selecciones principales.</span></span></label>
                    <div class="flex justify-end gap-2 pt-2"><flux:button type="button" variant="ghost" wire:click="cancelForm">Cancelar</flux:button><flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">Guardar</flux:button></div>
                </form>
            </aside>
        @endif
    </div>

    <flux:modal name="delete-skill" wire:model="confirmingDelete" class="max-w-md"><div class="space-y-5"><div><flux:heading size="lg">Eliminar habilidad</flux:heading><flux:text variant="subtle" class="mt-1">“{{ $deletingSkillName }}” también se desvinculará de todos los proyectos.</flux:text></div><div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</flux:button></div></div></flux:modal>
</div>
