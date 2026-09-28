@extends('layouts.admin')

@section('title', 'Fiche agent')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Fiche agent public</h1>
        <p>{{ $user->name }}</p>
    </div>

    <div class="dashboard-card mb-4">
        <h2>Informations de l’agent</h2>

        <table class="rca-table mt-3">
            <tr><th>Nom complet</th><td>{{ $user->name }}</td></tr>
            <tr><th>Email</th><td>{{ $user->email }}</td></tr>
            <tr><th>Téléphone</th><td>{{ $user->phone ?? '-' }}</td></tr>
            <tr><th>Rôle</th><td>{{ ucfirst($user->role) }}</td></tr>
            <tr>
                <th>Statut</th>
                <td>
                    @if($user->active)
                        <span class="badge-status validee">Actif</span>
                    @else
                        <span class="badge-status rejetee">Inactif</span>
                    @endif
                </td>
            </tr>
            <tr><th>Date création</th><td>{{ $user->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>

        <form method="POST" action="{{ route('admin.agents.toggle', $user) }}" class="mt-3">
            @csrf

            <button type="submit" class="btn-rca-secondary">
                {{ $user->active ? 'Désactiver le compte' : 'Réactiver le compte' }}
            </button>

            <a href="{{ route('admin.agents.index') }}" class="btn-rca-primary">
                Retour
            </a>
        </form>
    </div>

    <div class="table-card">
        <h2>Dossiers récents de la plateforme</h2>

        <table class="rca-table mt-3">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Citoyen</th>
                    <th>Démarche</th>
                    <th>Statut</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td><strong>{{ $application->reference }}</strong></td>
                        <td>{{ $application->user->name ?? '-' }}</td>
                        <td>{{ $application->procedure->title ?? '-' }}</td>
                        <td>
                            <span class="badge-status {{ $application->status }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                        <td>{{ $application->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucun dossier enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>
@endsection