@props([
    'icon' => '📭',
    'title' => 'Aucune donnée',
    'message' => null,
])

<div {{ $attributes->class(['component-empty-state']) }}>

    <span class="component-empty-icon">
        {{ $icon }}
    </span>

    <h3>{{ $title }}</h3>

    @if($message)
        <p>{{ $message }}</p>
    @endif

    @isset($action)
        <div class="component-empty-action">
            {{ $action }}
        </div>
    @endisset

</div>