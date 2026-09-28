@extends('layouts.admin')

@section('title', 'Gestion des ministères')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Gestion des ministères</h1>
        <p>Créez, consultez et administrez les ministères connectés à la plateforme.</p>
    </div>

    <div class="table-card">

        <div class="card-title-row mb-3">
            <h2>Liste des ministères</h2>

            <a href="{{ route('admin.ministries.create') }}" class="btn-rca-primary">
                Nouveau ministère
            </a>
        </div>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Description</th>
                    <th>Démarches</th>
                    <th>Statut</th>
                    <th>Date création</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($ministries as $ministry)
                    <tr>
                        <td><strong>{{ $ministry->name }}</strong></td>
                        <td>{{ Str::limit($ministry->description, 60) }}</td>
                        <td>{{ $ministry->procedures_count }}</td>
                        <td>
                            @if($ministry->active)
                                <span class="badge-status validee">Actif</span>
                            @else
                                <span class="badge-status rejetee">Inactif</span>
                            @endif
                        </td>
                        <td>{{ $ministry->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.ministries.show', $ministry) }}" class="btn-table">
                                Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Aucun ministère enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</section>
@endsection