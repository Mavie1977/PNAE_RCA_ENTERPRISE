@extends('layouts.admin')

@section('title', 'Fiche annonce')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Fiche annonce</h1>
        <p>{{ $announcement->title }}</p>
    </div>

    <div class="dashboard-card mb-4">
        <h2>Informations de l’annonce</h2>

        <table class="rca-table mt-3">
            <tr><th>Titre</th><td>{{ $announcement->title }}</td></tr>
            <tr><th>Type</th><td>{{ ucfirst($announcement->type) }}</td></tr>
            <tr><th>Contenu</th><td>{!! nl2br(e($announcement->content ?? '-')) !!}</td></tr>
            <tr>
                <th>Période</th>
                <td>
                    {{ $announcement->start_date ? $announcement->start_date->format('d/m/Y') : '-' }}
                    →
                    {{ $announcement->end_date ? $announcement->end_date->format('d/m/Y') : '-' }}
                </td>
            </tr>
            <tr>
                <th>Statut</th>
                <td>
                    @if($announcement->active)
                        <span class="badge-status validee">Active</span>
                    @else
                        <span class="badge-status rejetee">Inactive</span>
                    @endif
                </td>
            </tr>
            <tr><th>Date création</th><td>{{ $announcement->created_at->format('d/m/Y H:i') }}</td></tr>
        </table>

        <form method="POST" action="{{ route('admin.announcements.toggle', $announcement) }}" class="mt-3">
            @csrf

            <button type="submit" class="btn-rca-secondary">
                {{ $announcement->active ? 'Désactiver' : 'Réactiver' }}
            </button>

            <a href="{{ route('admin.announcements.index') }}" class="btn-rca-primary">
                Retour
            </a>
        </form>
    </div>

</section>
@endsection