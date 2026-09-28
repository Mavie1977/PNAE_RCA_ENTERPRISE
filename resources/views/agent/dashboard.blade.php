@extends('layouts.agent')

@section('title', 'Tableau de bord Agent Public')

@section('content')
<section class="page-section">

    <x-page-header
        title="Tableau de bord Agent Public"
        subtitle="Consultez et traitez les demandes administratives déposées par les citoyens."
        kicker="Espace Agent Public"
    >
        <x-slot:actions>
            <x-action-button
                :href="route('agent.applications')"
                icon="📂"
            >
                Voir toutes les demandes
            </x-action-button>
        </x-slot:actions>
    </x-page-header>

    {{-- Statistiques --}}
    <div class="component-stat-grid">

        <x-stat-card
            label="Dossiers totaux"
            :value="$total"
            icon="📁"
            :href="route('agent.applications')"
            description="Tous les dossiers disponibles"
        />

        <x-stat-card
            label="Demandes soumises"
            :value="$soumise"
            icon="📥"
            :href="route('agent.applications', ['status' => 'soumise'])"
            description="Dossiers à examiner"
        />

        <x-stat-card
            label="En traitement"
            :value="$traitement"
            icon="⏳"
            :href="route('agent.applications', ['status' => 'en_traitement'])"
            description="Dossiers en cours"
        />

        <x-stat-card
            label="Demandes validées"
            :value="$validee"
            icon="✅"
            :href="route('agent.applications', ['status' => 'validee'])"
            description="Décisions favorables"
        />

        <x-stat-card
            label="Demandes rejetées"
            :value="$rejetee"
            icon="❌"
            :href="route('agent.applications', ['status' => 'rejetee'])"
            description="Décisions défavorables"
        />

        <x-stat-card
            label="Demandes terminées"
            :value="$terminee"
            icon="🏁"
            :href="route('agent.applications', ['status' => 'terminee'])"
            description="Dossiers clôturés"
        />

    </div>

    {{-- Accès rapides --}}
    <x-panel
        title="Accès rapides"
        subtitle="Accédez directement aux catégories de dossiers à traiter."
        icon="⚡"
    >
        <div class="enterprise-quick-grid">

            <a
                href="{{ route('agent.applications', ['status' => 'soumise']) }}"
                class="enterprise-quick-card"
            >
                <span>📥</span>
                <strong>Nouvelles demandes</strong>
                <small>
                    Consulter les dossiers récemment déposés.
                </small>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'en_traitement']) }}"
                class="enterprise-quick-card"
            >
                <span>⏳</span>
                <strong>Dossiers en traitement</strong>
                <small>
                    Continuer le traitement des dossiers ouverts.
                </small>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'validee']) }}"
                class="enterprise-quick-card"
            >
                <span>✅</span>
                <strong>Dossiers validés</strong>
                <small>
                    Consulter les demandes ayant reçu un avis favorable.
                </small>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'rejetee']) }}"
                class="enterprise-quick-card"
            >
                <span>❌</span>
                <strong>Dossiers rejetés</strong>
                <small>
                    Consulter les décisions défavorables.
                </small>
            </a>

            <a
                href="{{ route('agent.applications', ['status' => 'terminee']) }}"
                class="enterprise-quick-card"
            >
                <span>🏁</span>
                <strong>Dossiers terminés</strong>
                <small>
                    Consulter les dossiers administrativement clôturés.
                </small>
            </a>

            <a
                href="{{ route('agent.applications') }}"
                class="enterprise-quick-card"
            >
                <span>🔎</span>
                <strong>Recherche et filtres</strong>
                <small>
                    Rechercher un dossier par référence, citoyen ou démarche.
                </small>
            </a>

        </div>
    </x-panel>

    {{-- Demandes récentes --}}
    <x-table-wrapper
        title="Demandes récentes"
        subtitle="Derniers dossiers administratifs déposés sur la plateforme."
    >
        <x-slot:actions>
            <x-action-button
                :href="route('agent.applications')"
                size="small"
                icon="📂"
            >
                Voir toutes les demandes
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
                @forelse($applications as $application)
                    <tr>
                        <td>
                            <strong>
                                {{ $application->reference }}
                            </strong>
                        </td>

                        <td>
                            {{ $application->user->name ?? 'Citoyen inconnu' }}

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
                                :href="route('agent.applications.show', $application)"
                                variant="warning"
                                size="small"
                                icon="📝"
                            >
                                Traiter
                            </x-action-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-empty-state
                                icon="📭"
                                title="Aucune demande disponible"
                                message="Aucun dossier administratif n’est actuellement disponible."
                            >
                                <x-slot:action>
                                    <x-action-button
                                        :href="route('agent.applications')"
                                    >
                                        Actualiser la liste
                                    </x-action-button>
                                </x-slot:action>
                            </x-empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </x-table-wrapper>

</section>
@endsection