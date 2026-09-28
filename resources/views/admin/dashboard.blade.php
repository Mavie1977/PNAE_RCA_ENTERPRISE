@extends('layouts.admin')

@section('title', 'Tableau de bord exécutif')

@section('content')
<section class="page-section executive-dashboard">

    <x-page-header
        title="Tableau de bord exécutif"
        subtitle="Vue consolidée de la Plateforme Nationale d’Administration Électronique."
        kicker="Pilotage national"
    >
        <x-slot:actions>
            <form
                method="GET"
                action="{{ route('admin.search.index') }}"
                class="dashboard-search"
            >
                <input
                    type="search"
                    name="q"
                    placeholder="Recherche nationale..."
                    aria-label="Recherche nationale"
                    required
                >

                <button type="submit">
                    Rechercher
                </button>
            </form>
        </x-slot:actions>
    </x-page-header>

    {{-- Statistiques nationales --}}
    <div class="component-stat-grid component-stat-grid-four">

        <x-stat-card
            label="Citoyens"
            :value="$stats['citizens']"
            icon="👥"
            :href="route('admin.citizens.index')"
            description="Comptes citoyens"
        />

        <x-stat-card
            label="Agents publics"
            :value="$stats['agents']"
            icon="🧑‍💼"
            :href="route('admin.agents.index')"
            description="Agents et responsables"
        />

        <x-stat-card
            label="Ministères"
            :value="$stats['ministries']"
            icon="🏛️"
            :href="route('admin.ministries.index')"
            description="Institutions connectées"
        />

        <x-stat-card
            label="Démarches"
            :value="$stats['procedures']"
            icon="📋"
            :href="route('admin.procedures.index')"
            description="Services disponibles"
        />

        <x-stat-card
            label="Total dossiers"
            :value="$stats['applications']"
            icon="📁"
            description="Toutes les demandes"
        />

        <x-stat-card
            label="Dossiers soumis"
            :value="$stats['submitted']"
            icon="📥"
            description="En attente de traitement"
        />

        <x-stat-card
            label="En traitement"
            :value="$stats['processing']"
            icon="⏳"
            description="Dossiers ouverts"
        />

        <x-stat-card
            label="Dossiers validés"
            :value="$stats['validated']"
            icon="✅"
            description="Décisions favorables"
        />

    </div>

    {{-- Performance et répartition --}}
    <div class="dashboard-columns">

        <x-panel
            title="Performance nationale"
            subtitle="Taux calculés sur l’ensemble des dossiers enregistrés."
            icon="📈"
        >
            <div class="performance-list">

                <div class="performance-item">
                    <div class="performance-label">
                        <span>Taux de validation</span>
                        <strong>{{ $rates['validation'] }} %</strong>
                    </div>

                    <div class="progress-track">
                        <span
                            class="progress-value progress-success"
                            style="width: {{ min($rates['validation'], 100) }}%"
                        ></span>
                    </div>
                </div>

                <div class="performance-item">
                    <div class="performance-label">
                        <span>Dossiers en traitement</span>
                        <strong>{{ $rates['processing'] }} %</strong>
                    </div>

                    <div class="progress-track">
                        <span
                            class="progress-value progress-warning"
                            style="width: {{ min($rates['processing'], 100) }}%"
                        ></span>
                    </div>
                </div>

                <div class="performance-item">
                    <div class="performance-label">
                        <span>Taux de rejet</span>
                        <strong>{{ $rates['rejection'] }} %</strong>
                    </div>

                    <div class="progress-track">
                        <span
                            class="progress-value progress-danger"
                            style="width: {{ min($rates['rejection'], 100) }}%"
                        ></span>
                    </div>
                </div>

                <div class="performance-summary-grid">

                    <div class="performance-summary-item">
                        <span>Dossiers terminés</span>
                        <strong>{{ $stats['completed'] }}</strong>
                    </div>

                    <div class="performance-summary-item">
                        <span>Dossiers rejetés</span>
                        <strong>{{ $stats['rejected'] }}</strong>
                    </div>

                </div>

            </div>
        </x-panel>

        <x-panel
            title="Répartition des dossiers"
            subtitle="Situation nationale actuelle par statut."
            icon="📊"
        >
            @php
                $maxStatusCount = max(
                    1,
                    $statusCounts['soumise'],
                    $statusCounts['en_traitement'],
                    $statusCounts['validee'],
                    $statusCounts['rejetee'],
                    $statusCounts['terminee']
                );
            @endphp

            <div class="status-chart">

                @foreach([
                    'soumise' => 'Soumis',
                    'en_traitement' => 'En traitement',
                    'validee' => 'Validés',
                    'rejetee' => 'Rejetés',
                    'terminee' => 'Terminés',
                ] as $statusKey => $statusLabel)

                    @php
                        $height = (
                            $statusCounts[$statusKey] / $maxStatusCount
                        ) * 100;
                    @endphp

                    <div class="status-chart-item">

                        <div class="status-chart-column">
                            <span
                                class="status-chart-bar status-chart-{{ $statusKey }}"
                                style="height: {{ max($height, 4) }}%"
                            ></span>
                        </div>

                        <strong>
                            {{ $statusCounts[$statusKey] }}
                        </strong>

                        <small>
                            {{ $statusLabel }}
                        </small>

                    </div>

                @endforeach

            </div>
        </x-panel>

    </div>

    {{-- Actions rapides --}}
    <x-panel
        title="Actions rapides"
        subtitle="Accédez directement aux principales fonctions d’administration."
        icon="⚡"
    >
        <div class="enterprise-quick-grid">

            <a
                href="{{ route('admin.agents.create') }}"
                class="enterprise-quick-card"
            >
                <span>➕</span>
                <strong>Nouvel agent</strong>
                <small>
                    Créer un nouveau compte agent public.
                </small>
            </a>

            <a
                href="{{ route('admin.ministries.create') }}"
                class="enterprise-quick-card"
            >
                <span>🏛️</span>
                <strong>Nouveau ministère</strong>
                <small>
                    Ajouter une institution à la plateforme.
                </small>
            </a>

            <a
                href="{{ route('admin.procedures.create') }}"
                class="enterprise-quick-card"
            >
                <span>📋</span>
                <strong>Nouvelle démarche</strong>
                <small>
                    Configurer un nouveau service administratif.
                </small>
            </a>

            <a
                href="{{ route('admin.announcements.create') }}"
                class="enterprise-quick-card"
            >
                <span>📢</span>
                <strong>Nouvelle annonce</strong>
                <small>
                    Publier une information nationale.
                </small>
            </a>

            <a
                href="{{ route('admin.search.index') }}"
                class="enterprise-quick-card"
            >
                <span>🔎</span>
                <strong>Recherche globale</strong>
                <small>
                    Rechercher un compte, un dossier ou une démarche.
                </small>
            </a>

            <a
                href="{{ route('admin.audit.index') }}"
                class="enterprise-quick-card"
            >
                <span>🕘</span>
                <strong>Journal national</strong>
                <small>
                    Consulter l’historique des actions.
                </small>
            </a>

            <a
                href="{{ route('admin.settings.index') }}"
                class="enterprise-quick-card"
            >
                <span>⚙️</span>
                <strong>Paramètres</strong>
                <small>
                    Configurer les informations générales du portail.
                </small>
            </a>

        </div>
    </x-panel>

    {{-- Dossiers récents --}}
    <x-table-wrapper
        title="Derniers dossiers déposés"
        subtitle="Activité administrative récente de la plateforme."
    >
        <x-slot:actions>
            <x-action-button
                :href="route('admin.search.index')"
                size="small"
                icon="🔎"
            >
                Recherche globale
            </x-action-button>
        </x-slot:actions>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Citoyen</th>
                    <th>Démarche</th>
                    <th>Ministère</th>
                    <th>Statut</th>
                    <th>Documents</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($recentApplications as $application)
                    <tr>
                        <td>
                            <strong>
                                {{ $application->reference }}
                            </strong>
                        </td>

                        <td>
                            {{ $application->user->name ?? '-' }}

                            @if($application->user?->email)
                                <small class="table-secondary-text">
                                    {{ $application->user->email }}
                                </small>
                            @endif
                        </td>

                        <td>
                            {{ $application->procedure->title ?? '-' }}
                        </td>

                        <td>
                            {{ $application->procedure->ministry->name ?? '-' }}
                        </td>

                        <td>
                            <x-status-badge
                                :status="$application->status"
                            />
                        </td>

                        <td>
                            {{ $application->documents->count() }} pièce(s)
                        </td>

                        <td>
                            {{ $application->created_at?->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <x-action-button
                                :href="route('admin.applications.show', $application)"
                                size="small"
                                variant="warning"
                                icon="👁️"
                            >
                                Ouvrir
                            </x-action-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-empty-state
                                icon="📭"
                                title="Aucun dossier enregistré"
                                message="Aucune demande administrative n’a encore été déposée."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-wrapper>

    {{-- Utilisateurs récents --}}
    <x-panel
        title="Comptes récemment créés"
        subtitle="Derniers comptes enregistrés sur la plateforme."
        icon="👥"
    >
        <div class="recent-users-grid">

            @forelse($recentUsers as $user)
                <article class="recent-user-card">

                    <div class="recent-user-avatar">
                        {{ strtoupper(substr($user->name ?? '?', 0, 1)) }}
                    </div>

                    <div class="recent-user-content">
                        <strong>{{ $user->name }}</strong>
                        <small>{{ $user->email }}</small>

                        <div class="recent-user-meta">
                            <span class="role-badge">
                                {{ ucfirst($user->role) }}
                            </span>

                            <x-status-badge
                                :status="$user->active ? 'actif' : 'inactif'"
                            />
                        </div>
                    </div>

                </article>
            @empty
                <x-empty-state
                    icon="👤"
                    title="Aucun utilisateur récent"
                    message="Aucun compte récent n’est disponible."
                />
            @endforelse

        </div>
    </x-panel>

</section>
@endsection