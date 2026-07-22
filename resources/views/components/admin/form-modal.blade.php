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
    $modalClasses = trim("admin-form-modal {$sizeClass} ".$attributes->get('class', ''));
@endphp

<flux:modal :name="$name" wire:model="{{ $model }}" :class="$modalClasses" :closable="false">
    <div class="admin-form-modal__header">
        <div class="min-w-0">
            <h2 class="admin-form-modal__title">{{ $title }}</h2>
            @if (filled($description))
                <p class="admin-form-modal__description">{{ $description }}</p>
            @endif
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
