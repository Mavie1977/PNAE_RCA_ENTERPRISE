@extends('layouts.admin')

@section('title', 'Fiche citoyen')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Fiche citoyen</h1>
        <p>{{ $user->name }}</p>
    </div>

    <div class="dashboard-card mb-4">
        <h2>Informations du citoyen</h2>

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

        <form method="POST" action="{{ route('admin.citizens.toggle', $user) }}" class="mt-3">
            @csrf
            <button type="submit" class="btn-rca-secondary">
                {{ $user->active ? 'Désactiver le compte' : 'Réactiver le compte' }}
            </button>

            <a href="{{ route('admin.citizens.index') }}" class="btn-rca-primary">
                Retour
            </a>
        </form>
    </div>

    <div class="table-card">
        <h2>Demandes du citoyen</h2>

        <table class="rca-table mt-3">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Démarche</th>
                    <th>Statut</th>
                    <th>Documents</th>
                    <th>Date</th>
                </tr>
            </thead>

            <tbody>
                @forelse($user->applications as $application)
                    <tr>
                        <td><strong>{{ $application->reference }}</strong></td>
                        <td>{{ $application->procedure->title ?? '-' }}</td>
                        <td>
                            <span class="badge-status {{ $application->status }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                        <td>{{ $application->documents->count() }} pièce(s)</td>
                        <td>{{ $application->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center">Aucune demande déposée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>
@endsection