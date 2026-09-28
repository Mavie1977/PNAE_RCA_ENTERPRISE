@extends('layouts.admin')

@section('title', 'Recherche globale')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Recherche globale</h1>

        <p>
            Recherchez un citoyen, un agent, une demande,
            un ministère ou une démarche.
        </p>
    </div>

    <div class="enterprise-search-card">
        <form
            method="GET"
            action="{{ route('admin.search.index') }}"
            class="enterprise-search-form"
        >
            <input
                type="search"
                name="q"
                value="{{ $search }}"
                placeholder="Nom, email, référence, ministère, démarche..."
                required
                autofocus
            >

            <button type="submit">
                Rechercher
            </button>
        </form>
    </div>

    @if($search !== '')

        <div class="search-summary">
            Résultats pour :
            <strong>{{ $search }}</strong>
        </div>

        <div class="search-results-grid">

            <article class="search-result-card">
                <div class="search-result-header">
                    <h2>Utilisateurs</h2>
                    <span>{{ $users->count() }}</span>
                </div>

                @forelse($users as $user)
                    <div class="search-result-item">
                        <div>
                            <strong>{{ $user->name }}</strong>
                            <small>{{ $user->email }}</small>
                        </div>

                        <span class="role-badge">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                @empty
                    <p class="empty-result">
                        Aucun utilisateur trouvé.
                    </p>
                @endforelse
            </article>

            <article class="search-result-card">
                <div class="search-result-header">
                    <h2>Demandes</h2>
                    <span>{{ $applications->count() }}</span>
                </div>

                @forelse($applications as $application)
                    <a
                        href="{{ route('admin.applications.show', $application) }}"
                        class="search-result-item search-result-link"
                    >
                        <div>
                            <strong>{{ $application->reference }}</strong>

                            <small>
                                {{ $application->user->name ?? '-' }}
                                —
                                {{ $application->procedure->title ?? '-' }}
                            </small>
                        </div>

                        <span class="badge-status {{ $application->status }}">
                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        </span>
                    </a>
                @empty
                    <p class="empty-result">
                        Aucune demande trouvée.
                    </p>
                @endforelse
            </article>

            <article class="search-result-card">
                <div class="search-result-header">
                    <h2>Ministères</h2>
                    <span>{{ $ministries->count() }}</span>
                </div>

                @forelse($ministries as $ministry)
                    <a
                        href="{{ route('admin.ministries.show', $ministry) }}"
                        class="search-result-item search-result-link"
                    >
                        <div>
                            <strong>{{ $ministry->name }}</strong>

                            <small>
                                {{ $ministry->active ? 'Actif' : 'Inactif' }}
                            </small>
                        </div>

                        <span>🏛️</span>
                    </a>
                @empty
                    <p class="empty-result">
                        Aucun ministère trouvé.
                    </p>
                @endforelse
            </article>

            <article class="search-result-card">
                <div class="search-result-header">
                    <h2>Démarches</h2>
                    <span>{{ $procedures->count() }}</span>
                </div>

                @forelse($procedures as $procedure)
                    <a
                        href="{{ route('admin.procedures.show', $procedure) }}"
                        class="search-result-item search-result-link"
                    >
                        <div>
                            <strong>{{ $procedure->title }}</strong>

                            <small>
                                {{ $procedure->ministry->name ?? '-' }}
                            </small>
                        </div>

                        <span>📋</span>
                    </a>
                @empty
                    <p class="empty-result">
                        Aucune démarche trouvée.
                    </p>
                @endforelse
            </article>

        </div>

    @else

        <div class="empty-search-state">
            <span>🔎</span>

            <h2>Lancer une recherche nationale</h2>

            <p>
                Saisissez une référence, un nom, un email,
                un ministère ou une démarche.
            </p>
        </div>

    @endif

</section>
@endsection