@props([
    'label',
    'value',
    'icon' => null,
    'href' => null,
    'description' => null,
])

@php
    $tag = $href ? 'a' : 'div';
@endphp

<{{ $tag }}
    @if($href)
        href="{{ $href }}"
    @endif

    {{ $attributes->class([
        'component-stat-card',
        'component-stat-card-link' => $href,
    ]) }}
>

    @if($icon)
        <span class="component-stat-icon" aria-hidden="true">
            {{ $icon }}
        </span>
    @endif

    <div class="component-stat-content">
        <span class="component-stat-label">
            {{ $label }}
        </span>

        <strong class="component-stat-value">
            {{ $value }}
        </strong>

        @if($description)
            <small class="component-stat-description">
                {{ $description }}
            </small>
        @endif
    </div>

</{{ $tag }}>