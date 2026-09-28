<a
    href="{{ route('agent.dashboard') }}"
    class="sidebar-link {{
        request()->routeIs('agent.dashboard')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📊</span>
    <span>Tableau de bord</span>
</a>

<div class="sidebar-group-title">
    TRAITEMENT
</div>

<a
    href="{{ route('agent.applications') }}"
    class="sidebar-link {{
        request()->routeIs('agent.applications')
        && ! request('status')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📂</span>
    <span>Toutes les demandes</span>
</a>

<a
    href="{{ route('agent.applications', [
        'status' => 'soumise',
    ]) }}"
    class="sidebar-link {{
        request()->routeIs('agent.applications')
        && request('status') === 'soumise'
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📥</span>
    <span>Nouvelles demandes</span>
</a>

<a
    href="{{ route('agent.applications', [
        'status' => 'en_traitement',
    ]) }}"
    class="sidebar-link {{
        request()->routeIs('agent.applications')
        && request('status') === 'en_traitement'
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">⏳</span>
    <span>En traitement</span>
</a>

<a
    href="{{ route('agent.applications', [
        'status' => 'validee',
    ]) }}"
    class="sidebar-link {{
        request()->routeIs('agent.applications')
        && request('status') === 'validee'
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">✅</span>
    <span>Validées</span>
</a>

<a
    href="{{ route('agent.applications', [
        'status' => 'rejetee',
    ]) }}"
    class="sidebar-link {{
        request()->routeIs('agent.applications')
        && request('status') === 'rejetee'
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">❌</span>
    <span>Rejetées</span>
</a>

<a
    href="{{ route('agent.applications', [
        'status' => 'terminee',
    ]) }}"
    class="sidebar-link {{
        request()->routeIs('agent.applications')
        && request('status') === 'terminee'
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🏁</span>
    <span>Terminées</span>
</a>