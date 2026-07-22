<div class="space-y-8">
    <header class="flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between">
        <div class="space-y-1">
            <flux:text class="text-xs font-semibold uppercase tracking-[0.16em] text-zinc-500">Pulso editorial</flux:text>
            <flux:heading size="xl" level="1">Tu portafolio, de un vistazo</flux:heading>
            <flux:text variant="subtle">Revisa qué está publicado, qué necesita atención y quién escribió recientemente.</flux:text>
        </div>
        <div class="flex items-center gap-2 rounded-lg border border-zinc-200/80 bg-white px-3 py-2 text-sm dark:border-zinc-700 dark:bg-zinc-900">
            <span class="size-2 rounded-full bg-emerald-500" aria-hidden="true"></span>
            <span class="font-medium">{{ $stats['published'] }} publicados</span>
            <span class="text-zinc-400">de {{ $stats['projects'] }}</span>
        </div>
    </header>

    <section aria-labelledby="publication-heading" class="grid gap-4 lg:grid-cols-[minmax(0,1.5fr)_minmax(16rem,0.5fr)]">
        <div class="overflow-hidden rounded-xl border border-zinc-200/80 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between border-b border-zinc-200/70 px-5 py-4 dark:border-zinc-700">
                <div>
                    <flux:heading id="publication-heading" size="lg">Estado de publicación</flux:heading>
                    <flux:text variant="subtle" class="text-sm">El recorrido real del contenido antes de salir al público.</flux:text>
                </div>
                <span class="text-3xl font-semibold tabular-nums text-zinc-950 dark:text-white">{{ $stats['projects'] }}</span>
            </div>
            <div class="grid divide-y divide-zinc-200/70 sm:grid-cols-3 sm:divide-x sm:divide-y-0 dark:divide-zinc-700">
                <div class="p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">En vitrina</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $stats['published'] }}</p>
                    <p class="mt-1 text-sm text-zinc-500">Proyectos visibles</p>
                </div>
                <div class="p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">En mesa</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $stats['drafts'] }}</p>
                    <p class="mt-1 text-sm text-zinc-500">Borradores pendientes</p>
                </div>
                <div class="p-5">
                    <p class="text-xs font-semibold uppercase tracking-wider text-violet-600 dark:text-violet-400">Portada</p>
                    <p class="mt-2 text-2xl font-semibold tabular-nums">{{ $stats['featured'] }}</p>
                    <p class="mt-1 text-sm text-zinc-500">Proyectos destacados</p>
                </div>
            </div>
        </div>

        <div class="rounded-xl border border-zinc-200/80 bg-zinc-50/70 p-5 dark:border-zinc-700 dark:bg-zinc-900">
            <p class="text-xs font-semibold uppercase tracking-wider text-zinc-500">Contenido conectado</p>
            <dl class="mt-5 space-y-4">
                <div class="flex items-center justify-between"><dt class="text-sm text-zinc-600 dark:text-zinc-300">Habilidades</dt><dd class="font-semibold tabular-nums">{{ $stats['skills'] }}</dd></div>
                <div class="flex items-center justify-between"><dt class="text-sm text-zinc-600 dark:text-zinc-300">Categorías</dt><dd class="font-semibold tabular-nums">{{ $stats['categories'] }}</dd></div>
                <div class="flex items-center justify-between"><dt class="text-sm text-zinc-600 dark:text-zinc-300">Experiencias</dt><dd class="font-semibold tabular-nums">{{ $stats['experiences'] }}</dd></div>
                <div class="flex items-center justify-between border-t border-zinc-200 pt-4 dark:border-zinc-700"><dt class="text-sm font-medium">Mensajes sin leer</dt><dd class="rounded-full bg-zinc-950 px-2 py-0.5 text-xs font-semibold tabular-nums text-white dark:bg-white dark:text-zinc-950">{{ $stats['unreadMessages'] }}</dd></div>
            </dl>
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-xl border border-zinc-200/80 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="px-5 pb-3 pt-5"><flux:heading size="lg">Proyectos recientes</flux:heading></div>
            @forelse ($recentProjects as $project)
                <div class="flex items-center gap-3 border-t border-zinc-200/70 px-5 py-3 dark:border-zinc-700" wire:key="dashboard-project-{{ $project->id }}">
                    <span class="size-2 rounded-full {{ $project->status === 'published' ? 'bg-emerald-500' : ($project->status === 'draft' ? 'bg-amber-500' : 'bg-zinc-400') }}" aria-hidden="true"></span>
                    <span class="sr-only">Estado: {{ $project->status === 'published' ? 'Publicado' : ($project->status === 'draft' ? 'Borrador' : 'Archivado') }}</span>
                    <div class="min-w-0 flex-1"><p class="truncate text-sm font-medium">{{ $project->title }}</p><p class="text-xs text-zinc-500">{{ $project->category?->name ?? 'Sin categoría' }}</p></div>
                    <time class="text-xs text-zinc-400" datetime="{{ $project->updated_at->toIso8601String() }}">{{ $project->updated_at->diffForHumans() }}</time>
                </div>
            @empty
                <div class="px-5 pb-6"><flux:text variant="subtle">Aún no hay proyectos para revisar.</flux:text></div>
            @endforelse
        </div>

        <div class="rounded-xl border border-zinc-200/80 bg-white dark:border-zinc-700 dark:bg-zinc-900">
            <div class="flex items-center justify-between px-5 pb-3 pt-5"><flux:heading size="lg">Conversaciones recientes</flux:heading><span class="text-xs font-medium text-zinc-500">{{ $stats['unreadMessages'] }} pendientes</span></div>
            @forelse ($recentMessages as $message)
                <div class="flex items-start gap-3 border-t border-zinc-200/70 px-5 py-3 dark:border-zinc-700" wire:key="dashboard-message-{{ $message->id }}">
                    <span class="mt-1.5 size-2 rounded-full {{ $message->is_read ? 'bg-zinc-300 dark:bg-zinc-600' : 'bg-sky-500' }}" aria-hidden="true"></span>
                    <span class="sr-only">{{ $message->is_read ? 'Leído' : 'Sin leer' }}</span>
                    <div class="min-w-0 flex-1"><p class="truncate text-sm font-medium">{{ $message->subject ?: 'Sin asunto' }}</p><p class="truncate text-xs text-zinc-500">{{ $message->name }} · {{ $message->email }}</p></div>
                    <time class="text-xs text-zinc-400" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time>
                </div>
            @empty
                <div class="px-5 pb-6"><flux:text variant="subtle">La bandeja está despejada.</flux:text></div>
            @endforelse
        </div>
    </section>
</div>
