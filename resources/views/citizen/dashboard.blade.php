@extends('layouts.citizen')

@section('title', 'Tableau de bord citoyen')

@section('content')

<section class="page-section">

    <div class="page-heading">
        <h1>Tableau de bord citoyen</h1>
        <p>Bienvenue dans votre espace personnel sécurisé.</p>
    </div>

    <div class="dashboard-grid">

        <div class="stat-card">
            <span>Total demandes</span>
            <strong>{{ $applications->count() }}</strong>
        </div>

        <div class="stat-card">
            <span>En traitement</span>
            <strong>{{ $applications->where('status', 'en_traitement')->count() }}</strong>
        </div>

        <div class="stat-card">
            <span>Validées</span>
            <strong>{{ $applications->where('status', 'validee')->count() }}</strong>
        </div>

        <div class="stat-card">
            <span>Rejetées</span>
            <strong>{{ $applications->where('status', 'rejetee')->count() }}</strong>
        </div>

    </div>

    <div class="table-card mt-4">

        <div class="card-title-row">
            <h2>Mes dernières demandes</h2>

            <div>
                <a href="{{ route('citizen.application.create') }}" class="btn-rca-primary">
                    Nouvelle demande
                </a>

                <a href="{{ route('citizen.applications') }}" class="btn-rca-secondary">
                    Mes demandes
                </a>
            </div>
        </div>

        <table class="rca-table mt-3">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Démarche</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($applications->take(5) as $application)
                    <tr>
                        <td>{{ $application->reference }}</td>
                        <td>{{ $application->procedure->title ?? 'Démarche administrative' }}</td>
                        <td>
                            <span class="badge-status {{ $application->status }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                        <td>{{ $application->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            Aucune demande enregistrée pour le moment.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</section>

@endsection