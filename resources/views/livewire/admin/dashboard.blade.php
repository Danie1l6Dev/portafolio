<div class="admin-page">
    <x-admin.page-header
        title="Dashboard"
        description="Bienvenido al panel de administración del portafolio."
    >
        <x-slot:actions>
            <x-admin.button :href="route('home')" variant="secondary" size="sm" target="_blank">
                <flux:icon.arrow-top-right-on-square class="size-4" />
                Ver portafolio
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <section aria-label="Métricas del portafolio" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 2xl:grid-cols-6">
        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Proyectos</p>
            <p class="my-2 text-3xl font-bold tabular-nums text-sky-700 dark:text-sky-300">{{ $stats['projects'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">{{ $stats['published'] }} publicados · {{ $stats['drafts'] }} borradores</p>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Habilidades</p>
            <p class="my-2 text-3xl font-bold tabular-nums text-sky-700 dark:text-sky-300">{{ $stats['skills'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Tecnologías registradas</p>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Experiencias</p>
            <p class="my-2 text-3xl font-bold tabular-nums text-sky-700 dark:text-sky-300">{{ $stats['experiences'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Trayectoria profesional</p>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Categorías</p>
            <p class="my-2 text-3xl font-bold tabular-nums text-sky-700 dark:text-sky-300">{{ $stats['categories'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Organización de proyectos</p>
        </article>

        <article class="rounded-xl border border-slate-200 bg-white p-5 shadow-sm dark:border-slate-700 dark:bg-slate-900">
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Logros</p>
            <p class="my-2 text-3xl font-bold tabular-nums text-sky-700 dark:text-sky-300">{{ $stats['achievements'] }}</p>
            <p class="text-xs text-slate-500 dark:text-slate-400">Certificados y reconocimientos</p>
        </article>

        <a
            href="{{ route('panel.messages') }}"
            wire:navigate
            class="group rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-[border-color,box-shadow,transform] duration-150 hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-sky-700"
        >
            <div class="flex items-start justify-between gap-3">
                <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Mensajes</p>
                @if ($stats['unreadMessages'] > 0)
                    <x-admin.badge variant="warning">{{ $stats['unreadMessages'] }} nuevos</x-admin.badge>
                @endif
            </div>
            <p class="my-2 text-3xl font-bold tabular-nums text-sky-700 dark:text-sky-300">{{ $stats['unreadMessages'] }}</p>
            <p class="text-xs text-slate-500 transition-colors group-hover:text-slate-700 dark:text-slate-400 dark:group-hover:text-slate-200">Conversaciones por revisar</p>
        </a>
    </section>

    <section aria-labelledby="quick-access-heading" class="space-y-3">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">Contenido</p>
            <h2 id="quick-access-heading" class="mt-1 text-sm font-semibold text-slate-900 dark:text-white">Accesos rápidos</h2>
        </div>

        <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            @foreach ([
                ['route' => 'panel.projects', 'icon' => 'folder-git-2', 'label' => 'Proyectos', 'description' => 'Crea, publica y organiza tus proyectos.'],
                ['route' => 'panel.categories', 'icon' => 'tag', 'label' => 'Categorías', 'description' => 'Clasifica el trabajo del portafolio.'],
                ['route' => 'panel.skills', 'icon' => 'code-bracket', 'label' => 'Habilidades', 'description' => 'Gestiona tecnologías y logotipos.'],
                ['route' => 'panel.experiences', 'icon' => 'briefcase', 'label' => 'Experiencias', 'description' => 'Actualiza tu trayectoria profesional.'],
                ['route' => 'panel.achievements', 'icon' => 'trophy', 'label' => 'Logros', 'description' => 'Publica certificados, premios y fotos.'],
                ['route' => 'panel.messages', 'icon' => 'inbox', 'label' => 'Mensajes', 'description' => 'Revisa las conversaciones recibidas.'],
            ] as $access)
                <a
                    href="{{ route($access['route']) }}"
                    wire:navigate
                    class="group flex min-h-24 items-center gap-4 rounded-xl border border-slate-200 bg-white p-5 shadow-sm transition-[border-color,box-shadow,transform] duration-150 hover:-translate-y-0.5 hover:border-sky-300 hover:shadow-md focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-sky-500 dark:border-slate-700 dark:bg-slate-900 dark:hover:border-sky-700"
                >
                    <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-sky-50 text-sky-600 transition-colors group-hover:bg-sky-100 dark:bg-sky-400/10 dark:text-sky-300 dark:group-hover:bg-sky-400/15">
                        <flux:icon :icon="$access['icon']" class="size-5" />
                    </span>
                    <span class="min-w-0">
                        <strong class="block text-sm font-semibold text-slate-900 dark:text-white">{{ $access['label'] }}</strong>
                        <span class="mt-1 block text-xs leading-5 text-slate-500 dark:text-slate-400">{{ $access['description'] }}</span>
                    </span>
                </a>
            @endforeach
        </div>
    </section>

    <section class="grid gap-6 lg:grid-cols-2" aria-label="Actividad reciente">
        <div class="admin-table-shell">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Proyectos recientes</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Últimos cambios del portafolio.</p>
                </div>
                <x-admin.button :href="route('panel.projects')" variant="ghost" size="sm" wire:navigate>Ver todos</x-admin.button>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($recentProjects as $project)
                    <a href="{{ route('panel.projects') }}" wire:navigate class="flex min-h-16 items-center gap-3 px-5 py-3 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/60" wire:key="dashboard-project-{{ $project->id }}">
                        <span class="size-2 shrink-0 rounded-full {{ $project->status === 'published' ? 'bg-emerald-500' : ($project->status === 'draft' ? 'bg-amber-500' : 'bg-slate-400') }}" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $project->title }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $project->category?->name ?? 'Sin categoría' }}</p>
                        </div>
                        <time class="shrink-0 text-xs tabular-nums text-slate-400" datetime="{{ $project->updated_at->toIso8601String() }}">{{ $project->updated_at->diffForHumans() }}</time>
                    </a>
                @empty
                    <div class="admin-empty">Aún no hay proyectos para revisar.</div>
                @endforelse
            </div>
        </div>

        <div class="admin-table-shell">
            <div class="flex items-center justify-between gap-4 border-b border-slate-100 px-5 py-4 dark:border-slate-800">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Mensajes recientes</h2>
                    <p class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">Conversaciones recibidas desde el portafolio.</p>
                </div>
                <x-admin.button :href="route('panel.messages')" variant="ghost" size="sm" wire:navigate>Ver bandeja</x-admin.button>
            </div>

            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @forelse ($recentMessages as $message)
                    <a href="{{ route('panel.messages') }}" wire:navigate class="flex min-h-16 items-center gap-3 px-5 py-3 transition-colors hover:bg-slate-50 dark:hover:bg-slate-800/60" wire:key="dashboard-message-{{ $message->id }}">
                        <span class="size-2 shrink-0 rounded-full {{ $message->is_read ? 'bg-slate-300 dark:bg-slate-600' : 'bg-sky-500' }}" aria-hidden="true"></span>
                        <div class="min-w-0 flex-1">
                            <p class="truncate text-sm font-medium text-slate-900 dark:text-white">{{ $message->subject ?: 'Sin asunto' }}</p>
                            <p class="truncate text-xs text-slate-500 dark:text-slate-400">{{ $message->name }} · {{ $message->email }}</p>
                        </div>
                        <time class="shrink-0 text-xs tabular-nums text-slate-400" datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time>
                    </a>
                @empty
                    <div class="admin-empty">La bandeja está despejada.</div>
                @endforelse
            </div>
        </div>
    </section>
</div>
