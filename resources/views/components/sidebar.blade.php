@props([
    'role',
])

<aside
    class="workspace-sidebar"
    id="workspaceSidebar"
>
    <div class="sidebar-header">
        <span class="sidebar-role">
            @switch($role)

                @case('admin')
                    ADMINISTRATION NATIONALE
                    @break

                @case('responsable')
                    ESPACE RESPONSABLE
                    @break

                @case('agent')
                    ESPACE AGENT PUBLIC
                    @break

                @case('citizen')
                    ESPACE CITOYEN
                    @break

                @default
                    ESPACE SÉCURISÉ

            @endswitch
        </span>
    </div>

    <nav class="sidebar-nav">

        @switch($role)

            @case('admin')
                @include('components.sidebar.admin')
                @break

            @case('responsable')
                @include('components.sidebar.responsable')
                @break

            @case('agent')
                @include('components.sidebar.agent')
                @break

            @case('citizen')
                @include('components.sidebar.citizen')
                @break

            @default
                <div class="sidebar-empty">
                    Aucun menu disponible.
                </div>

        @endswitch

    </nav>
</aside>