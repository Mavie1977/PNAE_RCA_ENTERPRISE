@props([
    'title' => null,
    'subtitle' => null,
])

<div {{ $attributes->class(['component-table-wrapper']) }}>

    @if($title || $subtitle || isset($actions))
        <div class="component-table-heading">

            <div>
                @if($title)
                    <h2>{{ $title }}</h2>
                @endif

                @if($subtitle)
                    <p>{{ $subtitle }}</p>
                @endif
            </div>

            @isset($actions)
                <div class="component-table-actions">
                    {{ $actions }}
                </div>
            @endisset

        </div>
    @endif

    <div class="component-table-responsive">
        {{ $slot }}
    </div>

    @isset($pagination)
        <div class="component-table-pagination">
            {{ $pagination }}
        </div>
    @endisset

</div>