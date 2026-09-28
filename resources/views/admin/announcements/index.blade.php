@extends('layouts.admin')

@section('title', 'Gestion des annonces')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Gestion des annonces nationales</h1>
        <p>Publiez les messages importants visibles sur le portail public.</p>
    </div>

    <div class="table-card">

        <div class="card-title-row mb-3">
            <h2>Liste des annonces</h2>

            <a href="{{ route('admin.announcements.create') }}" class="btn-rca-primary">
                Nouvelle annonce
            </a>
        </div>

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Titre</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Statut</th>
                    <th>Date création</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($announcements as $announcement)
                    <tr>
                        <td><strong>{{ $announcement->title }}</strong></td>
                        <td>{{ ucfirst($announcement->type) }}</td>
                        <td>
                            {{ $announcement->start_date ? $announcement->start_date->format('d/m/Y') : '-' }}
                            →
                            {{ $announcement->end_date ? $announcement->end_date->format('d/m/Y') : '-' }}
                        </td>
                        <td>
                            @if($announcement->active)
                                <span class="badge-status validee">Active</span>
                            @else
                                <span class="badge-status rejetee">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $announcement->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.announcements.show', $announcement) }}" class="btn-table">
                                Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center">Aucune annonce enregistrée.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

    </div>

</section>
@endsection