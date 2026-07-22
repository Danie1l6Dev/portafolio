@props([
    'title',
    'description' => null,
    'count' => null,
])

<header {{ $attributes->class('admin-page-header') }}>
    <div class="admin-page-header__copy">
        <div class="admin-page-header__title-row">
            <h1 class="admin-page-header__title">{{ $title }}</h1>
            @if (! is_null($count))
                <span class="admin-page-header__count">{{ $count }}</span>
            @endif
        </div>

        @if (filled($description))
            <p class="admin-page-header__description">{{ $description }}</p>
        @endif
    </div>

    @isset($actions)
        <div class="admin-page-header__actions">
            {{ $actions }}
        </div>
    @endisset
</header>
