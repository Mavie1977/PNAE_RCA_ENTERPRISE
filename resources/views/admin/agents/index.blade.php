@extends('layouts.admin')

@section('title', 'Gestion des agents')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Gestion des agents publics</h1>
        <p>Créez, consultez, activez ou désactivez les comptes agents.</p>
    </div>

    <div class="table-card">

        <div class="card-title-row mb-3">
            <h2>Liste des agents</h2>
            <a href="{{ route('admin.agents.create') }}" class="btn-rca-primary">
                Nouvel agent
            </a>
        </div>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Statut</th>
                    <th>Date création</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($agents as $agent)
                    <tr>
                        <td><strong>{{ $agent->name }}</strong></td>
                        <td>{{ $agent->email }}</td>
                        <td>{{ $agent->phone ?? '-' }}</td>
                        <td>
                            @if($agent->active)
                                <span class="badge-status validee">Actif</span>
                            @else
                                <span class="badge-status rejetee">Inactif</span>
                            @endif
                        </td>
                        <td>{{ $agent->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.agents.show', $agent) }}" class="btn-table">
                                Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucun agent enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</section>
@endsection