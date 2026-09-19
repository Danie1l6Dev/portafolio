<div class="admin-page">
    <x-admin.page-header
        title="Hoja de vida"
        description="Sube el PDF que los visitantes descargan desde el botón «Descargar hoja de vida» del portafolio."
    />

    <section class="space-y-6" aria-label="Hoja de vida vigente">
        <div class="admin-table-shell p-5">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div class="flex items-center gap-4">
                    <span class="grid size-12 place-items-center rounded-xl bg-slate-100 text-slate-500 dark:bg-slate-800 dark:text-slate-300" aria-hidden="true">
                        <flux:icon.document-text class="size-6" />
                    </span>
                    <div>
                        <p class="font-medium text-slate-900 dark:text-white">Versión publicada</p>
                        <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            @if ($current['source'] === 'uploaded')
                                Subida desde el panel
                            @else
                                PDF original incluido en el proyecto
                            @endif
                            @if ($current['size'])
                                · {{ number_format($current['size'] / 1024, 0, ',', '.') }} KB
                            @endif
                            @if ($current['updated_at'])
                                · Actualizada el {{ \Illuminate\Support\Carbon::createFromTimestamp($current['updated_at'], config('app.timezone'))->translatedFormat('j \d\e F \d\e Y') }}
                            @endif
                        </p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2">
                    <x-admin.button variant="secondary" size="sm" :href="$current['url']" target="_blank" rel="noopener noreferrer">
                        <flux:icon.arrow-up-right class="size-4" />
                        Ver PDF actual
                    </x-admin.button>

                    @if ($current['source'] === 'uploaded')
                        <x-admin.button variant="danger-ghost" size="sm" wire:click="confirmReset">
                            Restaurar la original
                        </x-admin.button>
                    @endif
                </div>
            </div>
        </div>

        <form wire:submit="save" class="admin-table-shell space-y-4 p-5" novalidate>
            <div>
                <h2 class="font-medium text-slate-900 dark:text-white">Reemplazar hoja de vida</h2>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                    Solo archivos PDF de hasta {{ (int) (config('admin.resume.max_file_kilobytes') / 1024) }} MB. Al guardar, el cambio se publica de inmediato.
                </p>
            </div>

            <flux:input wire:model="resume" type="file" accept="application/pdf,.pdf" label="Nuevo PDF" />

            <div wire:loading.flex wire:target="resume" class="admin-loading">
                <flux:icon.loading class="size-4" />
                Subiendo archivo…
            </div>

            @if ($resume && ! $errors->has('resume'))
                <p class="text-xs text-slate-500 dark:text-slate-400">Listo para publicar: {{ $resume->getClientOriginalName() }}</p>
            @endif

            <div class="flex justify-end border-t border-slate-100 pt-4 dark:border-slate-800">
                <x-admin.button type="submit" variant="primary" wire:loading.attr="disabled" wire:target="save,resume">
                    Publicar hoja de vida
                </x-admin.button>
            </div>
        </form>
    </section>

    <x-admin.form-modal
        name="reset-resume"
        model="confirmingReset"
        title="Restaurar la hoja de vida original"
        description="Se eliminará el PDF que subiste y el portafolio volverá a ofrecer el PDF original incluido en el proyecto."
        close-action="cancelReset"
        size="sm"
    >
        <div class="flex justify-end gap-3">
            <x-admin.button type="button" variant="secondary" wire:click="cancelReset">Cancelar</x-admin.button>
            <x-admin.button type="button" variant="danger" wire:click="resetToDefault">Restaurar</x-admin.button>
        </div>
    </x-admin.form-modal>
</div>
