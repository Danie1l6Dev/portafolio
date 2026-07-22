<div class="admin-page">
    <x-admin.page-header
        title="Experiencias"
        description="Administra tu trayectoria profesional, los cargos y los resultados que quieres destacar."
        :count="$experiences->total()"
    >
        <x-slot:actions>
            <x-admin.button variant="primary" size="sm" wire:click="create">
                <flux:icon.plus class="size-4" />
                Nueva experiencia
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="space-y-4" aria-label="Listado de experiencias">
        <div class="admin-toolbar grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto]">
            <flux:input
                wire:model.live.debounce.300ms="search"
                icon="magnifying-glass"
                placeholder="Buscar empresa, cargo o ubicación"
                aria-label="Buscar experiencias"
            />

            <label class="flex min-h-10 cursor-pointer items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-600 transition-colors hover:border-slate-300 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:border-slate-600">
                <input type="checkbox" wire:model.live="currentOnly" class="size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 dark:border-slate-600" />
                <span>Solo actuales</span>
            </label>
        </div>

        <div wire:loading.flex wire:target="search,currentOnly,save,delete" class="admin-loading">
            <flux:icon.loading class="size-4" />
            Actualizando experiencias…
        </div>

        <div class="admin-table-shell hidden md:block">
            <div class="overflow-x-auto">
                <table class="admin-table min-w-[52rem]">
                    <thead>
                        <tr>
                            <th>Cargo</th>
                            <th>Empresa</th>
                            <th>Periodo</th>
                            <th>Ubicación</th>
                            <th>Estado</th>
                            <th class="text-right">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($experiences as $experience)
                            <tr wire:key="experience-table-{{ $experience->id }}">
                                <td>
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $experience->position }}</p>
                                    <p class="mt-0.5 max-w-xs truncate text-xs text-slate-400 dark:text-slate-500">{{ $experience->description ?: 'Sin descripción registrada' }}</p>
                                </td>
                                <td>
                                    <div class="flex items-center gap-3">
                                        @if ($experience->company_logo)
                                            <img class="size-8 rounded-lg object-contain ring-1 ring-slate-200 dark:ring-slate-700" src="{{ asset('storage/'.ltrim($experience->company_logo, '/')) }}" alt="Logo de {{ $experience->company }}" />
                                        @else
                                            <span class="grid size-8 shrink-0 place-items-center rounded-lg bg-slate-100 text-xs font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ str($experience->company)->substr(0, 1)->upper() }}</span>
                                        @endif
                                        <span class="font-medium text-slate-700 dark:text-slate-200">{{ $experience->company }}</span>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap text-xs tabular-nums text-slate-500 dark:text-slate-400">{{ $experience->duration }}</td>
                                <td class="text-slate-500 dark:text-slate-400">{{ $experience->location ?: '—' }}</td>
                                <td>
                                    @if ($experience->is_current)
                                        <x-admin.badge variant="success">Actual</x-admin.badge>
                                    @else
                                        <x-admin.badge variant="default">Finalizada</x-admin.badge>
                                    @endif
                                </td>
                                <td>
                                    <div class="flex justify-end gap-2">
                                        <x-admin.button variant="secondary" size="sm" wire:click="edit({{ $experience->id }})">Editar</x-admin.button>
                                        <x-admin.button variant="danger" size="sm" wire:click="confirmDelete({{ $experience->id }})">Eliminar</x-admin.button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6">
                                    <div class="admin-empty">
                                        <strong class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Tu trayectoria está lista para empezar</strong>
                                        <span class="mt-1 block text-xs">Añade el primer cargo o cambia los filtros.</span>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <div class="grid gap-3 md:hidden">
            @forelse ($experiences as $experience)
                <article wire:key="experience-card-{{ $experience->id }}" class="rounded-xl border border-slate-200 bg-white p-4 shadow-sm dark:border-slate-700 dark:bg-slate-900">
                    <div class="flex items-start gap-3">
                        @if ($experience->company_logo)
                            <img class="size-10 shrink-0 rounded-lg object-contain ring-1 ring-slate-200 dark:ring-slate-700" src="{{ asset('storage/'.ltrim($experience->company_logo, '/')) }}" alt="Logo de {{ $experience->company }}" />
                        @else
                            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-slate-100 text-sm font-bold text-slate-500 dark:bg-slate-800 dark:text-slate-300">{{ str($experience->company)->substr(0, 1)->upper() }}</span>
                        @endif

                        <div class="min-w-0 flex-1">
                            <div class="flex flex-wrap items-center gap-2">
                                <h2 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $experience->position }}</h2>
                                @if ($experience->is_current)
                                    <x-admin.badge variant="success">Actual</x-admin.badge>
                                @endif
                            </div>
                            <p class="mt-1 text-sm font-medium text-slate-600 dark:text-slate-300">{{ $experience->company }}</p>
                            <p class="mt-1 text-xs tabular-nums text-slate-400">{{ $experience->duration }}@if($experience->location) · {{ $experience->location }}@endif</p>
                        </div>
                    </div>

                    <p class="mt-3 line-clamp-2 text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $experience->description ?: 'Sin descripción registrada.' }}</p>

                    <div class="mt-4 flex justify-end gap-2 border-t border-slate-100 pt-3 dark:border-slate-800">
                        <x-admin.button variant="secondary" size="sm" wire:click="edit({{ $experience->id }})">Editar</x-admin.button>
                        <x-admin.button variant="danger" size="sm" wire:click="confirmDelete({{ $experience->id }})" aria-label="Eliminar experiencia en {{ $experience->company }}">
                            <flux:icon.trash class="size-4" />
                        </x-admin.button>
                    </div>
                </article>
            @empty
                <div class="admin-empty rounded-xl border border-dashed border-slate-300 dark:border-slate-700">
                    <strong class="block text-sm font-semibold text-slate-700 dark:text-slate-200">Tu trayectoria está lista para empezar</strong>
                    <span class="mt-1 block text-xs">Añade el primer cargo o cambia los filtros.</span>
                </div>
            @endforelse
        </div>

        <div class="admin-pagination">{{ $experiences->links() }}</div>
    </section>

    <x-admin.form-modal
        name="experience-form"
        model="showForm"
        :title="$editingExperienceId ? 'Editar experiencia' : 'Nueva experiencia'"
        description="Registra el cargo con fechas, contexto y resultados concretos."
        close-action="cancelForm"
        size="lg"
    >
        <form wire:submit="save" class="admin-form">
            <div class="admin-form-grid">
                <flux:input wire:model="company" label="Empresa" required autofocus />
                <flux:input wire:model="position" label="Cargo" required />
            </div>

            <div class="admin-form-grid">
                <flux:input wire:model="location" label="Ubicación" />
                <flux:input wire:model="companyUrl" label="Sitio de la empresa" type="url" placeholder="https://" />
            </div>

            <flux:textarea wire:model="description" label="Responsabilidades y logros" rows="5" />

            <div class="admin-form-grid">
                <flux:input wire:model="startedAt" label="Inicio" type="date" />
                <flux:input wire:model="finishedAt" label="Fin" type="date" :disabled="$isCurrent" />
            </div>

            <label class="flex cursor-pointer items-start gap-3 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm dark:border-slate-700 dark:bg-slate-800/60">
                <input type="checkbox" wire:model.live="isCurrent" class="mt-0.5 size-4 rounded border-slate-300 text-sky-600 focus:ring-sky-500 dark:border-slate-600" />
                <span>
                    <strong class="block font-medium text-slate-800 dark:text-slate-100">Es mi experiencia actual</strong>
                    <span class="mt-0.5 block text-xs text-slate-500 dark:text-slate-400">La fecha de finalización quedará abierta.</span>
                </span>
            </label>

            <div class="admin-form-grid items-end">
                <flux:input wire:model="companyLogo" label="Logo de la empresa" type="file" accept="image/jpeg,image/png,image/webp" />
                <flux:input wire:model="sortOrder" label="Orden" type="number" min="0" />
            </div>

            <div wire:loading.flex wire:target="companyLogo" class="admin-loading">
                <flux:icon.loading class="size-4" />
                Preparando logotipo…
            </div>

            @if ($companyLogo)
                <p class="text-xs text-slate-500 dark:text-slate-400">Nuevo logo listo para subir: {{ $companyLogo->getClientOriginalName() }}</p>
            @elseif($editingExperience?->company_logo)
                <div class="flex items-center gap-3 rounded-lg border border-slate-200 bg-slate-50 p-3 dark:border-slate-700 dark:bg-slate-800/60">
                    <img class="h-14 max-w-40 rounded-lg object-contain ring-1 ring-black/10 dark:ring-white/10" src="{{ asset('storage/'.ltrim($editingExperience->company_logo, '/')) }}" alt="Logo actual de {{ $editingExperience->company }}" />
                    <span class="text-xs text-slate-500 dark:text-slate-400">Logo actual</span>
                </div>
            @endif

            <div class="flex justify-end gap-3 border-t border-slate-100 pt-4 dark:border-slate-800">
                <x-admin.button type="button" variant="secondary" wire:click="cancelForm">Cancelar</x-admin.button>
                <x-admin.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,companyLogo">Guardar experiencia</x-admin.button>
            </div>
        </form>
    </x-admin.form-modal>

    <x-admin.form-modal
        name="delete-experience"
        model="confirmingDelete"
        title="Eliminar experiencia"
        :description="'Se eliminará “'.$deletingExperienceName.'” y su material asociado.'"
        close-action="cancelDelete"
        size="sm"
    >
        <div class="flex justify-end gap-3">
            <x-admin.button variant="secondary" wire:click="cancelDelete">Cancelar</x-admin.button>
            <x-admin.button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</x-admin.button>
        </div>
    </x-admin.form-modal>
</div>
