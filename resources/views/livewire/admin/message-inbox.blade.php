<div class="admin-page">
    <x-admin.page-header
        title="Bandeja de contacto"
        description="Revisa conversaciones, identifica pendientes y responde con todo el contexto."
        :count="$messages->total()"
    >
        <x-slot:actions>
            <x-admin.button
                variant="secondary"
                wire:click="markAllAsRead"
                :disabled="$unreadCount === 0"
                wire:loading.attr="disabled"
                wire:target="markAllAsRead"
            >
                <flux:icon.check-circle class="size-4" />
                Marcar todo como leído
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    @if ($unreadCount > 0)
        <div class="admin-inbox-summary">
            <span class="admin-inbox-summary__dot" aria-hidden="true"></span>
            <strong>{{ $unreadCount }} {{ $unreadCount === 1 ? 'conversación nueva' : 'conversaciones nuevas' }}</strong>
            <span>requieren revisión.</span>
        </div>
    @endif

    <div class="admin-inbox">
        <section class="admin-inbox__list {{ $selectedMessage ? 'hidden lg:flex' : 'flex' }}" aria-label="Lista de mensajes">
            <div class="admin-inbox__filters">
                <flux:input
                    wire:model.live.debounce.300ms="search"
                    icon="magnifying-glass"
                    placeholder="Buscar persona, correo o asunto"
                    aria-label="Buscar mensajes"
                />
                <div class="admin-filter-chips admin-filter-chips--full" role="group" aria-label="Filtrar mensajes">
                    @foreach (['all' => 'Todos', 'unread' => 'Nuevos', 'read' => 'Leídos'] as $value => $label)
                        <button
                            type="button"
                            wire:click="$set('filter', '{{ $value }}')"
                            class="admin-filter-chip {{ $filter === $value ? 'is-active' : '' }}"
                            aria-pressed="{{ $filter === $value ? 'true' : 'false' }}"
                        >
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div wire:loading.flex wire:target="search,filter,selectMessage,markAsRead,delete" class="admin-loading admin-loading--flat">
                <flux:icon.loading class="size-4" />
                Sincronizando bandeja…
            </div>

            <div class="admin-inbox__messages">
                @forelse ($messages as $message)
                    <button
                        type="button"
                        wire:key="message-{{ $message->id }}"
                        wire:click="selectMessage({{ $message->id }})"
                        class="admin-message-preview {{ $selectedMessageId === $message->id ? 'is-active' : '' }}"
                        aria-pressed="{{ $selectedMessageId === $message->id ? 'true' : 'false' }}"
                    >
                        <span class="admin-message-preview__avatar" aria-hidden="true">
                            {{ str($message->name)->substr(0, 1)->upper() }}
                            @unless ($message->is_read)
                                <span class="admin-message-preview__unread"></span>
                            @endunless
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="flex items-center justify-between gap-3">
                                <span class="truncate text-sm {{ $message->is_read ? 'font-medium' : 'font-semibold' }} text-slate-900 dark:text-slate-100">{{ $message->name }}</span>
                                <time class="shrink-0 text-[11px] tabular-nums text-slate-400" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans(short: true) }}</time>
                            </span>
                            <span class="mt-1 block truncate text-sm font-medium text-slate-700 dark:text-slate-300">{{ $message->subject ?: 'Sin asunto' }}</span>
                            <span class="mt-1 block truncate text-xs text-slate-500 dark:text-slate-400">{{ str($message->body)->squish() }}</span>
                        </span>
                    </button>
                @empty
                    <div class="admin-empty admin-empty--flush">
                        <span class="admin-empty__icon"><flux:icon.inbox class="size-5" /></span>
                        <p class="admin-empty__title">Nada pendiente</p>
                        <p class="admin-empty__copy">No hay mensajes que coincidan con este filtro.</p>
                    </div>
                @endforelse
            </div>

            <div class="admin-inbox__pagination">{{ $messages->links() }}</div>
        </section>

        <section class="admin-inbox__detail {{ $selectedMessage ? 'flex' : 'hidden lg:flex' }}" aria-label="Detalle del mensaje">
            @if ($selectedMessage)
                <article class="flex min-h-0 flex-1 flex-col">
                    <header class="admin-message-detail__header">
                        <div class="flex min-w-0 items-start gap-3 sm:gap-4">
                            <button type="button" wire:click="closeMessage" class="admin-message-detail__back lg:hidden" aria-label="Volver a la bandeja">
                                <flux:icon.arrow-left class="size-4" />
                            </button>
                            <span class="admin-message-detail__avatar" aria-hidden="true">{{ str($selectedMessage->name)->substr(0, 1)->upper() }}</span>
                            <div class="min-w-0 flex-1">
                                <p class="admin-message-detail__eyebrow">Mensaje de {{ $selectedMessage->name }}</p>
                                <h2 class="admin-message-detail__subject">{{ $selectedMessage->subject ?: 'Sin asunto' }}</h2>
                                <a class="admin-message-detail__email" href="mailto:{{ $selectedMessage->email }}">{{ $selectedMessage->email }}</a>
                            </div>
                        </div>
                        <div class="flex shrink-0 gap-1">
                            <x-admin.button size="sm" variant="ghost" wire:click="closeMessage" aria-label="Cerrar mensaje" class="hidden lg:inline-flex">
                                <flux:icon.x-mark class="size-4" />
                            </x-admin.button>
                            <x-admin.button size="sm" variant="danger-ghost" wire:click="confirmDelete({{ $selectedMessage->id }})" aria-label="Eliminar mensaje">
                                <flux:icon.trash class="size-4" />
                            </x-admin.button>
                        </div>
                    </header>

                    <div class="admin-message-detail__body">
                        <div class="max-w-3xl whitespace-pre-line text-[15px] leading-7 text-slate-700 dark:text-slate-200">{{ $selectedMessage->body }}</div>
                    </div>

                    <footer class="admin-message-detail__footer">
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            <time datetime="{{ $selectedMessage->created_at->toIso8601String() }}">{{ $selectedMessage->created_at->translatedFormat('d M Y · H:i') }}</time>
                            @if ($selectedMessage->ip_address)<span> · IP {{ $selectedMessage->ip_address }}</span>@endif
                        </div>
                        <x-admin.button
                            variant="primary"
                            href="mailto:{{ $selectedMessage->email }}?subject={{ rawurlencode('Re: '.($selectedMessage->subject ?: 'Tu mensaje desde mi portafolio')) }}"
                        >
                            <flux:icon.paper-airplane class="size-4" />
                            Responder por correo
                        </x-admin.button>
                    </footer>
                </article>
            @else
                <div class="admin-message-detail__empty">
                    <span class="admin-empty__icon admin-empty__icon--large"><flux:icon.envelope-open class="size-6" /></span>
                    <h2>Abre una conversación</h2>
                    <p>Selecciona un mensaje para leerlo completo y responder desde tu cliente de correo.</p>
                </div>
            @endif
        </section>
    </div>

    <x-admin.form-modal
        name="delete-message"
        model="confirmingDelete"
        title="Eliminar mensaje"
        description="Esta acción no se puede deshacer."
        close-action="cancelDelete"
        size="sm"
    >
        <p class="text-sm leading-6 text-slate-600 dark:text-slate-300">“{{ $deletingMessageSubject }}” se eliminará definitivamente de la bandeja.</p>
        <div class="admin-form__footer mt-6">
            <x-admin.button type="button" variant="secondary" wire:click="cancelDelete">Cancelar</x-admin.button>
            <x-admin.button type="button" variant="danger" wire:click="delete" wire:loading.attr="disabled" wire:target="delete">Eliminar mensaje</x-admin.button>
        </div>
    </x-admin.form-modal>
</div>
