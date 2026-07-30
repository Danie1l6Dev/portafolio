@props([
    'title',
    'description' => null,
    'closeAction' => null,
    'size' => 'md',
    'name',
    'model',
])

@php
    $sizeClass = match ($size) {
        'sm' => 'max-w-sm',
        'lg' => 'max-w-2xl',
        'xl' => 'max-w-3xl',
        default => 'max-w-lg',
    };
    $isScrollable = $size === 'xl';
    $modalClasses = trim("admin-form-modal admin-form-modal--size-{$size} ".($isScrollable ? 'admin-form-modal--scrollable ' : '')."{$sizeClass} ".$attributes->get('class', ''));
    $isDestructive = str_contains(strtolower($name), 'delete');
    $headerIcon = $isDestructive ? 'trash' : (str_contains(strtolower($title), 'editar') ? 'pencil-square' : 'plus');
@endphp

<flux:modal :name="$name" wire:model="{{ $model }}" :class="$modalClasses.($isDestructive ? ' admin-form-modal--danger' : '')" :closable="false">
    <div class="admin-form-modal__header">
        <div class="admin-form-modal__heading">
            <span class="admin-form-modal__icon" aria-hidden="true">
                <flux:icon :icon="$headerIcon" class="size-4" />
            </span>
            <div class="min-w-0">
            <h2 class="admin-form-modal__title">{{ $title }}</h2>
            @if (filled($description))
                <p class="admin-form-modal__description">{{ $description }}</p>
            @endif
            </div>
        </div>

        <flux:modal.close>
            <button
                type="button"
                class="admin-form-modal__close"
                @if (filled($closeAction)) wire:click="{{ $closeAction }}" @endif
                aria-label="Cerrar"
            >
                <flux:icon.x-mark class="size-4" />
            </button>
        </flux:modal.close>
    </div>

    <div class="admin-form-modal__body">
        {{ $slot }}
    </div>
</flux:modal>
