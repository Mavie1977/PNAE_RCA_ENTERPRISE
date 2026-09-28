@props([
    'href' => null,
    'type' => 'button',
    'variant' => 'primary',
    'size' => 'normal',
    'icon' => null,
    'target' => null,
])

@php
    $classes = [
        'component-action-button',
        'button-' . $variant,
        'button-' . $size,
    ];
@endphp

@if($href)
    <a
        href="{{ $href }}"

        @if($target)
            target="{{ $target }}"
        @endif

        {{ $attributes->class($classes) }}
    >
        @if($icon)
            <span class="component-button-icon">
                {{ $icon }}
            </span>
        @endif

        <span>{{ $slot }}</span>
    </a>
@else
    <button
        type="{{ $type }}"
        {{ $attributes->class($classes) }}
    >
        @if($icon)
            <span class="component-button-icon">
                {{ $icon }}
            </span>
        @endif

        <span>{{ $slot }}</span>
    </button>
@endif