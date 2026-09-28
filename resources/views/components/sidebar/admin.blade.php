<a
    href="{{ route('admin.dashboard') }}"
    class="sidebar-link {{
        request()->routeIs('admin.dashboard')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📊</span>
    <span>Tableau de bord</span>
</a>

<a
    href="{{ route('admin.search.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.search.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🔎</span>
    <span>Recherche globale</span>
</a>

<div class="sidebar-group-title">
    UTILISATEURS
</div>

<a
    href="{{ route('admin.citizens.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.citizens.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">👥</span>
    <span>Citoyens</span>
</a>

<a
    href="{{ route('admin.agents.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.agents.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🧑‍💼</span>
    <span>Agents publics</span>
</a>

<a
    href="{{ route('admin.users.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.users.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🛡️</span>
    <span>Utilisateurs</span>
</a>

<div class="sidebar-group-title">
    ORGANISATION
</div>

<a
    href="{{ route('admin.ministries.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.ministries.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🏛️</span>
    <span>Ministères</span>
</a>

<a
    href="{{ route('admin.procedures.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.procedures.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📋</span>
    <span>Démarches</span>
</a>

<div class="sidebar-group-title">
    COMMUNICATION
</div>

<a
    href="{{ route('admin.announcements.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.announcements.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📢</span>
    <span>Annonces</span>
</a>

<div class="sidebar-group-title">
    SUPERVISION
</div>

<a
    href="{{ route('admin.supervision.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.supervision.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🛰️</span>
    <span>Supervision nationale</span>
</a>

<a
    href="{{ route('admin.decision-dashboard.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.decision-dashboard.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">📈</span>
    <span>Pilotage décisionnel</span>
</a>

<a
    href="{{ route('admin.audit.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.audit.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🕘</span>
    <span>Journal national</span>
</a>

<a
    href="{{ route('admin.system-health.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.system-health.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">🩺</span>
    <span>Santé du système</span>
</a>

<a
    href="{{ route('admin.settings.index') }}"
    class="sidebar-link {{
        request()->routeIs('admin.settings.*')
            ? 'active'
            : ''
    }}"
>
    <span class="sidebar-icon">⚙️</span>
    <span>Paramètres</span>
</a>