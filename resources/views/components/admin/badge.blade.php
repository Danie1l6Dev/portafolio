@props([
    'variant' => 'default',
])

@php
    $resolvedVariant = match ($variant) {
        'primary', 'info', 'featured' => 'primary',
        'success', 'published' => 'success',
        'warning', 'draft' => 'warning',
        'danger' => 'danger',
        'neutral', 'archived' => 'neutral',
        default => 'default',
    };
@endphp

<span {{ $attributes->class("admin-badge admin-badge--{$resolvedVariant}") }} data-variant="{{ $resolvedVariant }}">
    {{ $slot }}
</span>
