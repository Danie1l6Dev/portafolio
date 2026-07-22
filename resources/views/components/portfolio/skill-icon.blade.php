@props([
    'icon' => null,
    'name' => '',
    'size' => 'md',
])

@php
    $iconValue = trim((string) $icon);
    $simpleIconSlug = str_starts_with($iconValue, 'si:') ? substr($iconValue, 3) : null;
    $hasSimpleIcon = filled($simpleIconSlug) && preg_match('/\A[a-z0-9-]+\z/i', $simpleIconSlug) === 1;
    $fallback = Illuminate\Support\Str::of($name)->trim()->substr(0, 2)->upper();
    $wrapperClass = $size === 'sm' ? 'size-5 rounded-md' : 'size-9 rounded-xl';
    $imageClass = $size === 'sm' ? 'size-3.5' : 'size-5';
    $imageDimension = $size === 'sm' ? 14 : 20;
@endphp

<span
    {{ $attributes->class([
        'relative inline-flex shrink-0 items-center justify-center overflow-hidden bg-paper-100 text-ink-500 ring-1 ring-inset ring-ink-950/5 dark:bg-white/[.08] dark:text-slate-300 dark:ring-white/10',
        $wrapperClass,
    ]) }}
    aria-hidden="true"
    data-skill-icon="{{ $hasSimpleIcon ? $simpleIconSlug : 'fallback' }}"
>
    @if ($hasSimpleIcon)
        <img
            src="https://cdn.simpleicons.org/{{ $simpleIconSlug }}"
            alt=""
            width="{{ $imageDimension }}"
            height="{{ $imageDimension }}"
            loading="lazy"
            decoding="async"
            referrerpolicy="no-referrer"
            class="{{ $imageClass }} object-contain"
        >
    @elseif (filled($iconValue))
        <span class="leading-none">{{ $iconValue }}</span>
    @else
        <span class="font-mono text-[0.62rem] font-semibold tracking-tight">{{ $fallback }}</span>
    @endif
</span>
