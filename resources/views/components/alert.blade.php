@props([
    'type' => 'info',
    'title' => null,
    'dismissible' => false,
])

@php
    $icons = [
        'success' => '✅',
        'warning' => '⚠️',
        'error' => '⛔',
        'danger' => '⛔',
        'info' => 'ℹ️',
    ];

    $icon = $icons[$type] ?? 'ℹ️';
@endphp

<div
    {{ $attributes->class([
        'component-alert',
        'component-alert-' . $type,
    ]) }}
    role="alert"
>

    <span class="component-alert-icon">
        {{ $icon }}
    </span>

    <div class="component-alert-content">

        @if($title)
            <strong>{{ $title }}</strong>
        @endif

        <div>
            {{ $slot }}
        </div>

    </div>

    @if($dismissible)
        <button
            type="button"
            class="component-alert-close"
            onclick="this.parentElement.remove()"
            aria-label="Fermer"
        >
            ×
        </button>
    @endif

</div>