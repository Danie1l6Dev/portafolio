<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><flux:heading size="xl" level="1">Línea profesional</flux:heading><flux:text variant="subtle">Edita la secuencia que explica dónde has trabajado y qué construiste allí.</flux:text></div>
        <flux:button variant="primary" icon="plus" wire:click="create">Nueva experiencia</flux:button>
    </header>

    <div class="grid gap-6 {{ $showForm ? 'xl:grid-cols-[minmax(0,1fr)_26rem]' : '' }}">
        <section class="min-w-0 space-y-4" aria-label="Listado de experiencias">
            <div class="grid gap-3 sm:grid-cols-[minmax(0,1fr)_auto]"><flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar empresa, cargo o ubicación" aria-label="Buscar experiencias" /><label class="flex min-h-10 items-center gap-2 rounded-lg border border-zinc-200 bg-white px-3 text-sm dark:border-zinc-700 dark:bg-zinc-900"><input type="checkbox" wire:model.live="currentOnly" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600" /><span>Solo actuales</span></label></div>

            <div wire:loading.flex wire:target="search,currentOnly,save,delete" class="items-center gap-2 rounded-lg border border-zinc-200 bg-white px-4 py-3 text-sm text-zinc-500 dark:border-zinc-700 dark:bg-zinc-900"><flux:icon.loading class="size-4" /> Actualizando la línea…</div>

            <div class="relative space-y-3 before:absolute before:bottom-6 before:left-[1.18rem] before:top-6 before:w-px before:bg-zinc-200 dark:before:bg-zinc-700">
                @forelse ($experiences as $experience)
                    <article wire:key="experience-{{ $experience->id }}" class="relative ml-10 rounded-xl border border-zinc-200/80 bg-white p-5 dark:border-zinc-700 dark:bg-zinc-900">
                        <span class="absolute -left-[2.45rem] top-6 grid size-5 place-items-center rounded-full bg-white ring-1 ring-zinc-300 dark:bg-zinc-900 dark:ring-zinc-600"><span class="size-2 rounded-full {{ $experience->is_current ? 'bg-emerald-500' : 'bg-zinc-400' }}"></span></span>
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                            <div class="min-w-0"><div class="flex flex-wrap items-center gap-2"><flux:heading size="lg">{{ $experience->position }}</flux:heading>@if($experience->is_current)<span class="rounded-full bg-emerald-50 px-2 py-0.5 text-[11px] font-semibold text-emerald-700 dark:bg-emerald-400/10 dark:text-emerald-300">Actual</span>@endif</div><p class="mt-1 font-medium text-zinc-600 dark:text-zinc-300">{{ $experience->company }}@if($experience->location) · {{ $experience->location }}@endif</p><p class="mt-2 line-clamp-2 text-sm leading-6 text-zinc-500">{{ $experience->description ?: 'Sin descripción registrada.' }}</p></div>
                            <div class="flex shrink-0 items-center gap-2"><time class="text-xs font-medium tabular-nums text-zinc-500">{{ $experience->duration }}</time><flux:button size="sm" variant="ghost" icon="pencil-square" wire:click="edit({{ $experience->id }})" aria-label="Editar experiencia en {{ $experience->company }}" /><flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $experience->id }})" aria-label="Eliminar experiencia en {{ $experience->company }}" /></div>
                        </div>
                    </article>
                @empty
                    <div class="ml-10 rounded-xl border border-dashed border-zinc-300 px-6 py-12 text-center dark:border-zinc-700"><flux:heading size="lg">Tu línea profesional está lista para empezar</flux:heading><flux:text variant="subtle" class="mt-1">Añade el primer cargo o cambia los filtros.</flux:text></div>
                @endforelse
            </div>
            {{ $experiences->links() }}
        </section>

        @if ($showForm)
            <aside class="h-fit rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-900" aria-label="Formulario de experiencia">
                <div class="mb-5"><flux:heading size="lg">{{ $editingExperienceId ? 'Editar experiencia' : 'Nueva experiencia' }}</flux:heading><flux:text variant="subtle" class="text-sm">Cuenta el cargo con fechas, contexto y resultados.</flux:text></div>
                <form wire:submit="save" class="space-y-4">
                    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-1"><flux:input wire:model="company" label="Empresa" required autofocus /><flux:input wire:model="position" label="Cargo" required /></div>
                    <flux:input wire:model="location" label="Ubicación" />
                    <flux:textarea wire:model="description" label="Responsabilidades y logros" rows="5" />
                    <flux:input wire:model="companyUrl" label="Sitio de la empresa" type="url" placeholder="https://" />
                    <div class="grid grid-cols-2 gap-4"><flux:input wire:model="startedAt" label="Inicio" type="date" /><flux:input wire:model="finishedAt" label="Fin" type="date" :disabled="$isCurrent" /></div>
                    <label class="flex items-center gap-3 rounded-lg border border-zinc-200 bg-white px-3 py-3 text-sm dark:border-zinc-700 dark:bg-zinc-950"><input type="checkbox" wire:model.live="isCurrent" class="rounded border-zinc-300 text-zinc-900 focus:ring-zinc-500 dark:border-zinc-600" /><span>Es mi experiencia actual</span></label>
                    <div class="grid grid-cols-[1fr_7rem] gap-4"><flux:input wire:model="companyLogo" label="Logo" type="file" accept="image/jpeg,image/png,image/webp" /><flux:input wire:model="sortOrder" label="Orden" type="number" min="0" /></div>
                    @if ($companyLogo)<p class="text-xs text-zinc-500">Nuevo logo listo para subir: {{ $companyLogo->getClientOriginalName() }}</p>@elseif($editingExperience?->company_logo)<img class="h-14 max-w-40 rounded-lg object-contain ring-1 ring-black/10" src="{{ asset('storage/'.ltrim($editingExperience->company_logo, '/')) }}" alt="Logo actual de {{ $editingExperience->company }}" />@endif
                    <div class="flex justify-end gap-2 pt-2"><flux:button type="button" variant="ghost" wire:click="cancelForm">Cancelar</flux:button><flux:button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,companyLogo">Guardar</flux:button></div>
                </form>
            </aside>
        @endif
    </div>

    <flux:modal name="delete-experience" wire:model="confirmingDelete" class="max-w-md"><div class="space-y-5"><div><flux:heading size="lg">Eliminar experiencia</flux:heading><flux:text variant="subtle" class="mt-1">Se eliminará “{{ $deletingExperienceName }}” y su material asociado.</flux:text></div><div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</flux:button></div></div></flux:modal>
</div>
