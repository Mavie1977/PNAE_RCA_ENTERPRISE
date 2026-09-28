<a
    href="{{ route('citizen.dashboard') }}"
    class="sidebar-link {{
        request()->routeIs('citizen.dashboard')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📊</span>
    <span>Tableau de bord</span>
</a>

<div class="sidebar-group-title">
    MES DÉMARCHES
</div>

<a
    href="{{ route('citizen.payments.index') }}"
    class="sidebar-link {{
        request()->routeIs('citizen.payments.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">💳</span>
    <span>Mes paiements</span>
</a>

<a
    href="{{ route('citizen.application.create') }}"
    class="sidebar-link {{
        request()->routeIs('citizen.application.create')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">➕</span>
    <span>Nouvelle demande</span>
</a>

<a
    href="{{ route('citizen.applications') }}"
    class="sidebar-link {{
        request()->routeIs('citizen.applications')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📁</span>
    <span>Mes demandes</span>
</a>

<a
    href="{{ route('services') }}"
    class="sidebar-link {{
        request()->routeIs('services')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🏛️</span>
    <span>Catalogue des services</span>
</a>