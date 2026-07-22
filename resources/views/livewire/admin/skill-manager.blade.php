<div class="admin-page">
    <x-admin.page-header
        title="Habilidades"
        description="Gestiona las tecnologías, herramientas y logotipos visibles en el portafolio."
        :count="$skills->total()"
    >
        <x-slot:actions>
            <x-admin.button variant="primary" size="sm" wire:click="create">
                <flux:icon.plus class="size-4" />
                Nueva habilidad
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="space-y-4" aria-label="Listado de habilidades">
        <div class="admin-toolbar grid gap-3 md:grid-cols-[minmax(0,1fr)_13rem_auto]">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Buscar habilidad"
                aria-label="Buscar habilidades"
            />

            <flux:select wire:model.live="groupFilter" aria-label="Filtrar por grupo">
                <flux:select.option value="">Todos los grupos</flux:select.option>
                @foreach ($groups as $groupName)
                    <flux:select.option value="{{ $groupName }}">{{ $groupName }}</flux:select.option>
                @endforeach
            </flux:select>

            <label class="flex min-h-10 cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-600 transition-colors hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-slate-600">
                <input type="checkbox" wire:model.live="featuredOnly" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 dark:border-slate-600" />
                <span>Solo destacadas</span>
            </label>
        </div>

        <div wire:loading.flex wire:target="search,groupFilter,featuredOnly,save,delete" class="admin-loading">
            <flux:icon.loading class="size-4" />
            Actualizando habilidades…
        </div>

        <div class="admin-table-shell hidden md:block">
            <div class="overflow-x-auto">
                <table class="admin-table min-w-[42rem]">
                    <thead>
                        <tr>
                            <th>Habilidad</th>
                            <th>Grupo</th>
                            <th>Proyectos</th>
                            <th>Visibilidad</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($skills as $skill)
                            <tr wire:key="skill-table-{{ $skill->id }}">
                                <td>
                                    <div class="flex items-center gap-3">
                                        <span class="grid size-9 shrink-0 place-items-center rounded-lg bg-slate-50 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                                            <x-portfolio.skill-icon :icon="$skill->icon" :name="$skill->name" />
                                        </span>
                                        <div class="min-w-0">
                                            <p class="font-medium text-slate-900 dark:text-white">{{ $skill->name }}</p>
                                            <p class="mt-0.5 truncate font-mono text-xs text-slate-400 dark:text-slate-500">{{ $skill->icon ?: 'icono automático' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-slate-600 dark:text-slate-300">{{ $skill->group ?: 'Sin grupo' }}</td>
                                <td class="tabular-nums text-slate-500 dark:text-slate-400">{{ $skill->projects_count }}</td>
                                <td>
                                    @if ($skill->is_featured)
                                        <x-admin.badge variant="primary">Destacada</x-admin.badge>
                                    @else
                                        <span class="text-xs text-slate-400">Normal</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-admin.button variant="secondary" size="sm" wire:click="edit({{ $skill->id }})">Editar</x-admin.button>
                                        <x-admin.button variant="danger" size="sm" wire:click="confirmDelete({{ $skill->id }})">Eliminar</x-admin.button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5">
                                    <div class="admin-empty">
                                        <strong class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Sin coincidencias técnicas</strong>
                                        <span class="mt-1 block text-xs">Ajusta los filtros o registra una habilidad.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-3 md:hidden">
            @forelse ($skills as $skill)
                <article wire:key="skill-card-{{ $skill->id }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start gap-3">
                        <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-slate-50 ring-1 ring-slate-200 dark:bg-slate-800 dark:ring-slate-700">
                            <x-portfolio.skill-icon :icon="$skill->icon" :name="$skill->name" />
                        </span>
                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="truncate text-sm font-semibold text-slate-900 dark:text-white">{{ $skill->name }}</h2>
                                @if ($skill->is_featured)
                                    <x-admin.badge variant="primary">Destacada</x-admin.badge>
                                @endif
                            </div>
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">{{ $skill->group ?: 'Sin grupo' }} · {{ $skill->projects_count }} {{ $skill->projects_count === 1 ? 'proyecto' : 'proyectos' }}</p>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <x-admin.button variant="secondary" size="sm" wire:click="edit({{ $skill->id }})">Editar</x-admin.button>
                        <x-admin.button variant="danger" size="sm" wire:click="confirmDelete({{ $skill->id }})" aria-label="Eliminar {{ $skill->name }}">
                            <flux:icon.trash class="size-4" />
                        </x-admin.button>
                    </div>
                </article>
            @empty
                <div class="admin-empty rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                    <strong class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Sin coincidencias técnicas</strong>
                    <span class="mt-1 block text-xs">Ajusta los filtros o registra una habilidad.</span>
                </div>
            @endforelse
        </div>

        <div class="admin-pagination">{{ $skills->links() }}</div>
    </section>

    <x-admin.form-modal
        name="skill-form"
        model="showForm"
        :title="$editingSkillId ? 'Editar habilidad' : 'Nueva habilidad'"
        description="Define su grupo, logotipo y posición. Las habilidades se muestran sin niveles."
        close-action="cancelForm"
        size="md"
    >
        <form wire:submit="save" class="admin-form">
            <flux:input wire:model="name" label="Nombre" required autofocus />

            <div class="admin-form-grid">
                <flux:select wire:model="group" label="Grupo">
                    <flux:select.option value="">Sin grupo</flux:select.option>
                    @foreach ($groups as $groupName)
                        <flux:select.option value="{{ $groupName }}">{{ $groupName }}</flux:select.option>
                    @endforeach
                </flux:select>
                <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" />
            </div>

            <div class="grid grid-cols-[minmax(0,1fr)_auto] items-end gap-3">
                <flux:input
                    wire:model.live.debounce.300ms="icon"
                    label="Icono"
                    placeholder="si:laravel"
                    description="Usa si:nombre para cargar el logotipo oficial."
                />
                <div class="grid size-10 place-items-center rounded-lg border border-slate-200 bg-slate-50 dark:border-slate-700 dark:bg-slate-800" aria-label="Vista previa del icono">
                    <x-portfolio.skill-icon :icon="$icon" :name="$name ?: 'Tecnología'" />
                </div>
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-800/60">
                <input type="checkbox" wire:model="isFeatured" class="mt-0.5 size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 dark:border-slate-600" />
                <span>
                    <strong class="block font-medium text-slate-800 dark:text-slate-100">Destacar habilidad</strong>
                    <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">Aparecerá en selecciones principales del portafolio.</span>
                </span>
            </label>

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                <x-admin.button type="button" variant="secondary" wire:click="cancelForm">Cancelar</x-admin.button>
                <x-admin.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save">Guardar habilidad</x-admin.button>
            </div>
        </form>
    </x-admin.form-modal>

    <x-admin.form-modal
        name="delete-skill"
        model="confirmingDelete"
        title="Eliminar habilidad"
        :description="'“'.$deletingSkillName.'” también se desvinculará de todos los proyectos.'"
        close-action="cancelDelete"
        size="sm"
    >
        <div class="flex justify-end gap-3">
            <x-admin.button variant="secondary" wire:click="cancelDelete">Cancelar</x-admin.button>
            <x-admin.button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</x-admin.button>
        </div>
    </x-admin.form-modal>
</div>
