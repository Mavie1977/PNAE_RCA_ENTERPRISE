@props([
    'title' => null,
    'subtitle' => null,
    'icon' => null,
])

<section {{ $attributes->class(['component-panel']) }}>

    @if($title || $subtitle || $icon || isset($actions))
        <header class="component-panel-header">

            <div class="component-panel-heading">
                @if($icon)
                    <span class="component-panel-icon">
                        {{ $icon }}
                    </span>
                @endif

                <div>
                    @if($title)
                        <h2>{{ $title }}</h2>
                    @endif

                    @if($subtitle)
                        <p>{{ $subtitle }}</p>
                    @endif
                </div>
            </div>

            @isset($actions)
                <div class="component-panel-actions">
                    {{ $actions }}
                </div>
            @endisset

        </header>
    @endif

    <div class="component-panel-body">
        {{ $slot }}
    </div>

    @isset($footer)
        <footer class="component-panel-footer">
            {{ $footer }}
        </footer>
    @endisset

</section>