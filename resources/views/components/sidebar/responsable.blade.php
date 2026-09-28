<a
    href="{{ route('responsable.dashboard') }}"
    class="sidebar-link {{
        request()->routeIs('responsable.dashboard')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📊</span>
    <span>Supervision du ministère</span>
</a>

<div class="sidebar-group-title">
    PILOTAGE MINISTÉRIEL
</div>

<a
    href="{{ route('responsable.dashboard') }}#dossiers"
    class="sidebar-link"
>
    <span class="sidebar-icon">📁</span>
    <span>Dossiers du ministère</span>
</a>

<a
    href="{{ route('responsable.dashboard') }}#agents"
    class="sidebar-link"
>
    <span class="sidebar-icon">👥</span>
    <span>Agents du ministère</span>
</a>

<a
    href="{{ route('responsable.dashboard') }}#performance"
    class="sidebar-link"
>
    <span class="sidebar-icon">📈</span>
    <span>Performance</span>
</a>

<div class="sidebar-group-title">
    GESTION
</div>

<a
    href="{{ route('responsable.agents.index') }}"
    class="sidebar-link {{
        request()->routeIs('responsable.agents.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🧑‍💼</span>
    <span>Mes agents</span>
</a>
@if (auth()->user()->isResponsableFonctionPublique())

    <div class="sidebar-group-title">
        RECRUTEMENT NATIONAL
    </div>

    <a
        href="{{ route(
            'responsable.recruitment.agents.create'
        ) }}"
        class="sidebar-link {{
            request()->routeIs(
                'responsable.recruitment.agents.create'
            )
                ? 'active'
                : ''
        }}"
    >
        <span class="sidebar-icon">➕</span>
        <span>Recruter un agent</span>
    </a>

@endif