@props([
    'title',
    'subtitle' => null,
    'kicker' => null,
])

<div {{ $attributes->class(['component-page-header']) }}>

    <div class="component-page-header-content">

        @if($kicker)
            <span class="component-page-kicker">
                {{ $kicker }}
            </span>
        @endif

        <h1>{{ $title }}</h1>

        @if($subtitle)
            <p>{{ $subtitle }}</p>
        @endif

    </div>

    @isset($actions)
        <div class="component-page-header-actions">
            {{ $actions }}
        </div>
    @endisset

</div>