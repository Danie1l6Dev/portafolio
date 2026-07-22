@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'href' => null,
])

@php
    $resolvedVariant = in_array($variant, ['primary', 'secondary', 'ghost', 'danger', 'danger-ghost'], true)
        ? $variant
        : 'primary';
    $resolvedSize = in_array($size, ['sm', 'md', 'lg'], true) ? $size : 'md';
    $classes = "admin-button admin-button--{$resolvedVariant} admin-button--{$resolvedSize}";
@endphp

@if (filled($href))
    <a href="{{ $href }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </a>
@else
    <button type="{{ $type }}" {{ $attributes->class($classes) }}>
        {{ $slot }}
    </button>
@endif
