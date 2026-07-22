<div class="rounded-[1.75rem] border border-[#cfdae6] bg-white/90 p-5 shadow-[0_24px_70px_-42px_rgba(11,31,51,0.45)] sm:p-7 lg:p-8">
    <div class="mb-7">
        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-sky-700">Contacto directo</p>
        <h3 class="mt-2 text-2xl font-semibold tracking-tight text-[#0b1f33]">Cuéntame sobre tu proyecto</h3>
        <p class="mt-2 max-w-2xl text-sm leading-6 text-slate-600">
            Completa el formulario y responderé tan pronto como me sea posible.
        </p>
    </div>

    <div aria-live="polite" aria-atomic="true">
        @if ($successMessage)
            <div id="contact-form-success" class="mb-6 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm leading-6 text-emerald-900" role="status">
                {{ $successMessage }}
            </div>
        @endif
    </div>

    <div aria-live="assertive" aria-atomic="true">
        @if ($errorMessage)
            <div id="contact-form-error" class="mb-6 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm leading-6 text-rose-900" role="alert">
                {{ $errorMessage }}
            </div>
        @endif
    </div>

    <form wire:submit="submit" class="relative space-y-5" aria-label="Formulario de contacto">
        <div class="absolute -left-[10000px] top-auto h-px w-px overflow-hidden" aria-hidden="true">
            <label for="contact-website">No completes este campo</label>
            <input id="contact-website" type="text" wire:model="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <label for="contact-name" class="mb-2 block text-sm font-medium text-[#0b1f33]">
                    Nombre <span class="text-sky-700" aria-hidden="true">*</span>
                </label>
                <input
                    id="contact-name"
                    type="text"
                    wire:model.blur="name"
                    autocomplete="name"
                    required
                    minlength="2"
                    maxlength="100"
                    aria-required="true"
                    aria-invalid="{{ $errors->has('name') ? 'true' : 'false' }}"
                    @if ($errors->has('name')) aria-describedby="contact-name-error" @endif
                    class="block min-h-12 w-full rounded-xl border bg-[#f7fafc] px-4 py-3 text-base text-[#0b1f33] outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-100 {{ $errors->has('name') ? 'border-rose-400' : 'border-slate-300' }}"
                    placeholder="Tu nombre"
                >
                @error('name')
                    <p id="contact-name-error" class="mt-2 text-sm text-rose-700" role="alert">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="contact-email" class="mb-2 block text-sm font-medium text-[#0b1f33]">
                    Correo <span class="text-sky-700" aria-hidden="true">*</span>
                </label>
                <input
                    id="contact-email"
                    type="email"
                    wire:model.blur="email"
                    autocomplete="email"
                    required
                    maxlength="150"
                    aria-required="true"
                    aria-invalid="{{ $errors->has('email') ? 'true' : 'false' }}"
                    @if ($errors->has('email')) aria-describedby="contact-email-error" @endif
                    class="block min-h-12 w-full rounded-xl border bg-[#f7fafc] px-4 py-3 text-base text-[#0b1f33] outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-100 {{ $errors->has('email') ? 'border-rose-400' : 'border-slate-300' }}"
                    placeholder="nombre@correo.com"
                >
                @error('email')
                    <p id="contact-email-error" class="mt-2 text-sm text-rose-700" role="alert">{{ $message }}</p>
                @enderror
            </div>
        </div>

        <div>
            <label for="contact-subject" class="mb-2 block text-sm font-medium text-[#0b1f33]">
                Asunto <span class="text-sky-700" aria-hidden="true">*</span>
            </label>
            <input
                id="contact-subject"
                type="text"
                wire:model.blur="subject"
                required
                minlength="3"
                maxlength="150"
                aria-required="true"
                aria-invalid="{{ $errors->has('subject') ? 'true' : 'false' }}"
                @if ($errors->has('subject')) aria-describedby="contact-subject-error" @endif
                class="block min-h-12 w-full rounded-xl border bg-[#f7fafc] px-4 py-3 text-base text-[#0b1f33] outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-100 {{ $errors->has('subject') ? 'border-rose-400' : 'border-slate-300' }}"
                placeholder="¿En qué puedo ayudarte?"
            >
            @error('subject')
                <p id="contact-subject-error" class="mt-2 text-sm text-rose-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label for="contact-body" class="mb-2 block text-sm font-medium text-[#0b1f33]">
                Mensaje <span class="text-sky-700" aria-hidden="true">*</span>
            </label>
            <textarea
                id="contact-body"
                wire:model.blur="body"
                rows="6"
                required
                minlength="10"
                maxlength="3000"
                aria-required="true"
                aria-invalid="{{ $errors->has('body') ? 'true' : 'false' }}"
                aria-describedby="contact-body-help{{ $errors->has('body') ? ' contact-body-error' : '' }}"
                class="block min-h-40 w-full resize-y rounded-xl border bg-[#f7fafc] px-4 py-3 text-base leading-7 text-[#0b1f33] outline-none transition placeholder:text-slate-400 focus:border-sky-500 focus:ring-4 focus:ring-sky-100 {{ $errors->has('body') ? 'border-rose-400' : 'border-slate-300' }}"
                placeholder="Contexto, objetivos y cualquier detalle importante..."
            ></textarea>
            <div class="mt-2 flex items-start justify-between gap-4">
                <p id="contact-body-help" class="text-xs leading-5 text-slate-500">Entre 10 y 3000 caracteres.</p>
                <span class="text-xs text-slate-400" aria-hidden="true">Máx. 3000</span>
            </div>
            @error('body')
                <p id="contact-body-error" class="mt-2 text-sm text-rose-700" role="alert">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-3 pt-1 sm:flex-row sm:items-center sm:justify-between">
            <p class="text-xs leading-5 text-slate-500">Los campos marcados con * son obligatorios.</p>
            <button
                type="submit"
                wire:loading.attr="disabled"
                wire:target="submit"
                class="inline-flex min-h-12 items-center justify-center rounded-full bg-[#0b1f33] px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-sky-800 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-sky-600 disabled:cursor-wait disabled:opacity-65"
            >
                <span wire:loading.remove wire:target="submit">Enviar mensaje</span>
                <span wire:loading wire:target="submit" class="items-center gap-2" role="status">
                    <svg class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4Z"></path>
                    </svg>
                    Enviando…
                </span>
            </button>
        </div>
    </form>
</div>
