<div class="space-y-6">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div><div class="flex items-center gap-3"><flux:heading size="xl" level="1">Bandeja de contacto</flux:heading>@if($unreadCount > 0)<span class="rounded-full bg-sky-50 px-2.5 py-1 text-xs font-semibold tabular-nums text-sky-700 dark:bg-sky-400/10 dark:text-sky-300">{{ $unreadCount }} nuevos</span>@endif</div><flux:text variant="subtle">Lee el contexto, responde por correo y deja la bandeja en orden.</flux:text></div>
        <flux:button variant="ghost" icon="check-circle" wire:click="markAllAsRead" :disabled="$unreadCount === 0" wire:loading.attr="disabled" wire:target="markAllAsRead">Marcar todos como leídos</flux:button>
    </header>

    <div class="grid min-h-[34rem] overflow-hidden rounded-xl border border-zinc-200/80 bg-white lg:grid-cols-[minmax(20rem,0.82fr)_minmax(0,1.18fr)] dark:border-zinc-700 dark:bg-zinc-900">
        <section class="min-w-0 border-b border-zinc-200 lg:border-b-0 lg:border-r dark:border-zinc-700" aria-label="Lista de mensajes">
            <div class="space-y-3 border-b border-zinc-200 p-4 dark:border-zinc-700">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Buscar persona, correo o asunto" aria-label="Buscar mensajes" />
                <div class="grid grid-cols-3 rounded-lg bg-zinc-100 p-1 text-sm dark:bg-zinc-800" role="group" aria-label="Filtrar mensajes">
                    @foreach (['all' => 'Todos', 'unread' => 'Nuevos', 'read' => 'Leídos'] as $value => $label)
                        <button type="button" wire:click="$set('filter', '{{ $value }}')" class="min-h-9 rounded-md px-3 font-medium transition-colors {{ $filter === $value ? 'bg-white text-zinc-950 shadow-sm dark:bg-zinc-700 dark:text-white' : 'text-zinc-500 hover:text-zinc-900 dark:hover:text-white' }}" aria-pressed="{{ $filter === $value ? 'true' : 'false' }}">{{ $label }}</button>
                    @endforeach
                </div>
            </div>

            <div wire:loading.flex wire:target="search,filter,selectMessage,markAsRead,delete" class="items-center gap-2 border-b border-zinc-200 px-4 py-3 text-sm text-zinc-500 dark:border-zinc-700"><flux:icon.loading class="size-4" /> Sincronizando bandeja…</div>

            <div class="divide-y divide-zinc-200/70 dark:divide-zinc-700">
                @forelse ($messages as $message)
                    <button type="button" wire:key="message-{{ $message->id }}" wire:click="selectMessage({{ $message->id }})" class="flex w-full min-h-24 items-start gap-3 px-4 py-4 text-left transition-colors hover:bg-zinc-50 focus-visible:outline-2 focus-visible:outline-offset-[-2px] focus-visible:outline-zinc-900 dark:hover:bg-zinc-800/50 dark:focus-visible:outline-white {{ $selectedMessageId === $message->id ? 'bg-zinc-100 dark:bg-zinc-800' : '' }}">
                        <span class="mt-1.5 size-2 shrink-0 rounded-full {{ $message->is_read ? 'bg-zinc-300 dark:bg-zinc-600' : 'bg-sky-500' }}" aria-hidden="true"></span>
                        <span class="sr-only">{{ $message->is_read ? 'Leído' : 'Sin leer' }}</span>
                        <span class="min-w-0 flex-1"><span class="flex items-center justify-between gap-3"><span class="truncate text-sm font-semibold text-zinc-950 dark:text-white">{{ $message->name }}</span><time class="shrink-0 text-[11px] tabular-nums text-zinc-400" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time></span><span class="mt-1 block truncate text-sm font-medium text-zinc-700 dark:text-zinc-300">{{ $message->subject ?: 'Sin asunto' }}</span><span class="mt-1 block truncate text-xs text-zinc-500">{{ str($message->body)->squish() }}</span></span>
                    </button>
                @empty
                    <div class="px-6 py-14 text-center"><div class="mx-auto grid size-11 place-items-center rounded-full bg-zinc-100 dark:bg-zinc-800"><flux:icon.inbox class="size-5 text-zinc-400" /></div><flux:heading size="lg" class="mt-3">Nada pendiente</flux:heading><flux:text variant="subtle" class="mt-1">No hay mensajes que coincidan con este filtro.</flux:text></div>
                @endforelse
            </div>
            <div class="p-4">{{ $messages->links() }}</div>
        </section>

        <section class="min-w-0" aria-label="Detalle del mensaje">
            @if ($selectedMessage)
                <article class="flex h-full flex-col">
                    <header class="border-b border-zinc-200 p-5 dark:border-zinc-700">
                        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between"><div class="min-w-0"><p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Mensaje de {{ $selectedMessage->name }}</p><flux:heading size="xl" level="2" class="mt-1 text-balance">{{ $selectedMessage->subject ?: 'Sin asunto' }}</flux:heading><a class="mt-2 inline-block text-sm text-zinc-500 underline decoration-zinc-300 underline-offset-4 hover:text-zinc-950 dark:hover:text-white" href="mailto:{{ $selectedMessage->email }}">{{ $selectedMessage->email }}</a></div><div class="flex shrink-0 gap-1"><flux:button size="sm" variant="ghost" icon="x-mark" wire:click="closeMessage" aria-label="Cerrar mensaje" /><flux:button size="sm" variant="ghost" icon="trash" wire:click="confirmDelete({{ $selectedMessage->id }})" aria-label="Eliminar mensaje" /></div></div>
                    </header>
                    <div class="flex-1 p-5 sm:p-7"><div class="prose prose-zinc max-w-none whitespace-pre-line text-[15px] leading-7 text-zinc-700 dark:prose-invert dark:text-zinc-200">{{ $selectedMessage->body }}</div></div>
                    <footer class="flex flex-col gap-3 border-t border-zinc-200 bg-zinc-50/70 p-5 sm:flex-row sm:items-center sm:justify-between dark:border-zinc-700 dark:bg-zinc-800/30"><div class="text-xs text-zinc-500"><time datetime="{{ $selectedMessage->created_at->toIso8601String() }}">{{ $selectedMessage->created_at->translatedFormat('d M Y · H:i') }}</time>@if($selectedMessage->ip_address)<span> · IP {{ $selectedMessage->ip_address }}</span>@endif</div><flux:button variant="primary" icon="paper-airplane" href="mailto:{{ $selectedMessage->email }}?subject={{ rawurlencode('Re: '.($selectedMessage->subject ?: 'Tu mensaje desde mi portafolio')) }}">Responder por correo</flux:button></footer>
                </article>
            @else
                <div class="grid h-full min-h-[28rem] place-items-center px-8 text-center"><div><div class="mx-auto grid size-14 place-items-center rounded-full bg-zinc-100 dark:bg-zinc-800"><flux:icon.envelope-open class="size-6 text-zinc-400" /></div><flux:heading size="lg" class="mt-4">Abre una conversación</flux:heading><flux:text variant="subtle" class="mt-1 max-w-sm">Selecciona un mensaje para leerlo completo y responder desde tu cliente de correo.</flux:text></div></div>
            @endif
        </section>
    </div>

    <flux:modal name="delete-message" wire:model="confirmingDelete" class="max-w-md"><div class="space-y-5"><div><flux:heading size="lg">Eliminar mensaje</flux:heading><flux:text variant="subtle" class="mt-1">“{{ $deletingMessageSubject }}” se eliminará definitivamente de la bandeja.</flux:text></div><div class="flex justify-end gap-2"><flux:button variant="ghost" wire:click="cancelDelete">Cancelar</flux:button><flux:button variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar</flux:button></div></div></flux:modal>
</div>
