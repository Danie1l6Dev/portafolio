<div class="admin-page admin-dashboard">
    <x-admin.page-header
        title="Resumen"
        description="Una lectura rápida del estado editorial, la actividad y los próximos movimientos de tu portafolio."
    >
        <x-slot:actions>
            <x-admin.button :href="route('home')" variant="secondary" size="sm" target="_blank">
                <flux:icon.arrow-top-right-on-square class="size-4" />
                Ver portafolio
            </x-admin.button>
        </x-slot:actions>
    </x-admin.page-header>

    <section class="admin-dashboard-hero" aria-labelledby="dashboard-pulse-heading">
        <div class="admin-dashboard-hero__copy">
            <p class="admin-dashboard-hero__eyebrow">Centro de control</p>
            <h2 id="dashboard-pulse-heading">Tu portafolio está listo para seguir creciendo.</h2>
            <p>Consulta qué está publicado, atiende los mensajes pendientes y mantén el trabajo destacado al frente.</p>

            <div class="admin-dashboard-hero__actions">
                <x-admin.button :href="route('panel.projects')" variant="primary" size="md" wire:navigate>
                    <flux:icon.folder-git-2 class="size-4" />
                    Gestionar proyectos
                </x-admin.button>
                <a href="{{ route('panel.messages') }}" wire:navigate class="admin-dashboard-hero__link">
                    Revisar mensajes
                    <flux:icon.arrow-top-right-on-square class="size-3.5" />
                </a>
            </div>
        </div>

        <div class="admin-dashboard-pulse" aria-label="Estado de publicación">
            <div class="admin-dashboard-pulse__ring" style="--dashboard-progress: {{ $stats['publicationRate'] }}%;">
                <div>
                    <strong>{{ $stats['publicationRate'] }}%</strong>
                    <span>publicado</span>
                </div>
            </div>
            <div class="admin-dashboard-pulse__copy">
                <span>Estado de publicación</span>
                <strong>{{ $stats['published'] }} de {{ $stats['projects'] }} proyectos visibles</strong>
                <p>{{ $stats['featured'] }} {{ $stats['featured'] === 1 ? 'proyecto destacado' : 'proyectos destacados' }} en la selección principal.</p>
            </div>
        </div>
    </section>

    <section class="admin-dashboard-signals" aria-label="Señales del portafolio">
        <a href="{{ route('panel.projects') }}" wire:navigate class="admin-dashboard-signal admin-dashboard-signal--accent">
            <span class="admin-dashboard-signal__icon"><flux:icon.folder-git-2 class="size-5" /></span>
            <span class="admin-dashboard-signal__content">
                <span>Proyectos publicados</span>
                <strong>{{ $stats['published'] }}<small>/{{ $stats['projects'] }}</small></strong>
                <em>{{ $stats['drafts'] }} {{ $stats['drafts'] === 1 ? 'borrador por revisar' : 'borradores por revisar' }}</em>
            </span>
            <flux:icon.arrow-top-right-on-square class="admin-dashboard-signal__arrow size-4" />
        </a>

        <a href="{{ route('panel.messages') }}" wire:navigate class="admin-dashboard-signal {{ $stats['unreadMessages'] > 0 ? 'admin-dashboard-signal--attention' : '' }}">
            <span class="admin-dashboard-signal__icon"><flux:icon.inbox class="size-5" /></span>
            <span class="admin-dashboard-signal__content">
                <span>Bandeja de entrada</span>
                <strong>{{ $stats['unreadMessages'] }}</strong>
                <em>{{ $stats['unreadMessages'] > 0 ? 'mensajes esperan respuesta' : 'no hay mensajes pendientes' }}</em>
            </span>
            <flux:icon.arrow-top-right-on-square class="admin-dashboard-signal__arrow size-4" />
        </a>

        <a href="{{ route('panel.achievements') }}" wire:navigate class="admin-dashboard-signal">
            <span class="admin-dashboard-signal__icon"><flux:icon.trophy class="size-5" /></span>
            <span class="admin-dashboard-signal__content">
                <span>Logros y evidencia</span>
                <strong>{{ $stats['achievements'] }}</strong>
                <em>premios, certificados y galerías</em>
            </span>
            <flux:icon.arrow-top-right-on-square class="admin-dashboard-signal__arrow size-4" />
        </a>

        <a href="{{ route('panel.skills') }}" wire:navigate class="admin-dashboard-signal">
            <span class="admin-dashboard-signal__icon"><flux:icon.code-bracket class="size-5" /></span>
            <span class="admin-dashboard-signal__content">
                <span>Stack documentado</span>
                <strong>{{ $stats['skills'] }}</strong>
                <em>tecnologías para {{ $stats['contentItems'] }} piezas de contenido</em>
            </span>
            <flux:icon.arrow-top-right-on-square class="admin-dashboard-signal__arrow size-4" />
        </a>
    </section>

    <section class="admin-dashboard-workbench" aria-label="Estado editorial y acciones rápidas">
        <article class="admin-dashboard-publication">
            <div class="admin-dashboard-section-heading">
                <div>
                    <p>Visibilidad</p>
                    <h2>Estado editorial</h2>
                </div>
                <a href="{{ route('panel.projects') }}" wire:navigate>Ver proyectos <span aria-hidden="true">→</span></a>
            </div>

            <div class="admin-dashboard-publication__bar" aria-label="{{ $stats['publicationRate'] }} por ciento de proyectos publicados">
                <span style="width: {{ $stats['publicationRate'] }}%"></span>
            </div>

            <div class="admin-dashboard-publication__legend">
                <div>
                    <span class="admin-dashboard-publication__dot admin-dashboard-publication__dot--published"></span>
                    <span>Publicados</span>
                    <strong>{{ $stats['published'] }}</strong>
                </div>
                <div>
                    <span class="admin-dashboard-publication__dot admin-dashboard-publication__dot--draft"></span>
                    <span>Borradores</span>
                    <strong>{{ $stats['drafts'] }}</strong>
                </div>
                <div>
                    <span class="admin-dashboard-publication__dot admin-dashboard-publication__dot--featured"></span>
                    <span>Destacados</span>
                    <strong>{{ $stats['featured'] }}</strong>
                </div>
            </div>

            <p class="admin-dashboard-publication__note">
                @if ($stats['drafts'] > 0)
                    Tienes {{ $stats['drafts'] }} {{ $stats['drafts'] === 1 ? 'proyecto esperando publicación' : 'proyectos esperando publicación' }}.
                @elseif ($stats['projects'] > 0)
                    Todos tus proyectos registrados están publicados. Buen momento para actualizar la selección destacada.
                @else
                    Crea tu primer proyecto para comenzar a construir la sección pública.
                @endif
            </p>
        </article>

        <aside class="admin-dashboard-next">
            <div class="admin-dashboard-section-heading">
                <div>
                    <p>Siguiente paso</p>
                    <h2>Mantén el impulso</h2>
                </div>
            </div>

            <div class="admin-dashboard-next__list">
                <a href="{{ route('panel.projects') }}" wire:navigate>
                    <span><flux:icon.folder-plus class="size-4" /></span>
                    <span><strong>Nuevo proyecto</strong><small>Actualiza tu trabajo seleccionado.</small></span>
                    <span aria-hidden="true">→</span>
                </a>
                <a href="{{ route('panel.achievements') }}" wire:navigate>
                    <span><flux:icon.trophy class="size-4" /></span>
                    <span><strong>Agregar logro</strong><small>Documenta premios, fotos o certificados.</small></span>
                    <span aria-hidden="true">→</span>
                </a>
                <a href="{{ route('panel.experiences') }}" wire:navigate>
                    <span><flux:icon.briefcase class="size-4" /></span>
                    <span><strong>Revisar experiencia</strong><small>Mantén vigente tu trayectoria.</small></span>
                    <span aria-hidden="true">→</span>
                </a>
            </div>
        </aside>
    </section>

    <section class="admin-dashboard-activity" aria-label="Actividad reciente">
        <div class="admin-dashboard-section-heading admin-dashboard-section-heading--activity">
            <div>
                <p>Últimos movimientos</p>
                <h2>Actividad reciente</h2>
            </div>
            <span>Contenido y conversaciones del portafolio</span>
        </div>

        <div class="admin-dashboard-activity__grid">
            <article class="admin-dashboard-stream">
                <div class="admin-dashboard-stream__header">
                    <span class="admin-dashboard-stream__icon"><flux:icon.folder-git-2 class="size-4" /></span>
                    <div>
                        <h3>Proyectos recientes</h3>
                        <p>Últimos cambios realizados.</p>
                    </div>
                    <a href="{{ route('panel.projects') }}" wire:navigate>Ver todos</a>
                </div>

                <div class="admin-dashboard-stream__list">
                    @forelse ($recentProjects as $project)
                        <a href="{{ route('panel.projects') }}" wire:navigate wire:key="dashboard-project-{{ $project->id }}">
                            <span class="admin-dashboard-stream__status {{ $project->status === 'published' ? 'is-published' : ($project->status === 'draft' ? 'is-draft' : '') }}" aria-hidden="true"></span>
                            <span class="min-w-0 flex-1">
                                <strong>{{ $project->title }}</strong>
                                <small>{{ $project->category?->name ?? 'Sin categoría' }}</small>
                            </span>
                            <time datetime="{{ $project->updated_at->toIso8601String() }}">{{ $project->updated_at->diffForHumans() }}</time>
                        </a>
                    @empty
                        <div class="admin-dashboard-stream__empty">Aún no hay proyectos para revisar.</div>
                    @endforelse
                </div>
            </article>

            <article class="admin-dashboard-stream">
                <div class="admin-dashboard-stream__header">
                    <span class="admin-dashboard-stream__icon"><flux:icon.inbox class="size-4" /></span>
                    <div>
                        <h3>Mensajes recientes</h3>
                        <p>Conversaciones desde el portafolio.</p>
                    </div>
                    <a href="{{ route('panel.messages') }}" wire:navigate>Ver bandeja</a>
                </div>

                <div class="admin-dashboard-stream__list">
                    @forelse ($recentMessages as $message)
                        <a href="{{ route('panel.messages') }}" wire:navigate wire:key="dashboard-message-{{ $message->id }}">
                            <span class="admin-dashboard-stream__status {{ $message->is_read ? '' : 'is-unread' }}" aria-hidden="true"></span>
                            <span class="min-w-0 flex-1">
                                <strong>{{ $message->subject ?: 'Sin asunto' }}</strong>
                                <small>{{ $message->name }} · {{ $message->email }}</small>
                            </span>
                            <time datetime="{{ $message->created_at->toIso8601String() }}">{{ $message->created_at->diffForHumans() }}</time>
                        </a>
                    @empty
                        <div class="admin-dashboard-stream__empty">La bandeja está despejada.</div>
                    @endforelse
                </div>
            </article>
        </div>
    </section>
</div>
