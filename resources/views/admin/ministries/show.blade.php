@extends('layouts.admin')

@section('title', 'Fiche ministère')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Fiche ministère</h1>
        <p>{{ $ministry->name }}</p>
    </div>

    <div class="dashboard-card mb-4">
        <h2>Informations du ministère</h2>

        <table class="rca-table mt-3">
            <tr>
                <th>Nom</th>
                <td>{{ $ministry->name }}</td>
            </tr>

            <tr>
                <th>Slug</th>
                <td>{{ $ministry->slug }}</td>
            </tr>

            <tr>
                <th>Description</th>
                <td>{{ $ministry->description ?? '-' }}</td>
            </tr>

            <tr>
                <th>Statut</th>
                <td>
                    @if($ministry->active)
                        <span class="badge-status validee">Actif</span>
                    @else
                        <span class="badge-status rejetee">Inactif</span>
                    @endif
                </td>
            </tr>

            <tr>
                <th>Date création</th>
                <td>{{ $ministry->created_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>

        <form method="POST" action="{{ route('admin.ministries.toggle', $ministry) }}" class="mt-3">
            @csrf

            <button type="submit" class="btn-rca-secondary">
                {{ $ministry->active ? 'Désactiver' : 'Réactiver' }}
            </button>

            <a href="{{ route('admin.ministries.index') }}" class="btn-rca-primary">
                Retour
            </a>
			<a
    href="{{ route('admin.ministries.edit', $ministry) }}"
    class="btn-rca-primary"
>
    ✏️ Modifier le ministère
</a>
        </form>
    </div>

    <div class="table-card">
        <h2>Démarches liées à ce ministère</h2>

        <table class="rca-table mt-3">
            <thead>
                <tr>
                    <th>Démarche</th>
                    <th>Frais</th>
                    <th>Statut</th>
                    <th>Date création</th>
                </tr>
            </thead>

            <tbody>
                @forelse($ministry->procedures as $procedure)
                    <tr>
                        <td><strong>{{ $procedure->title }}</strong></td>
                        <td>{{ number_format($procedure->fee, 0, ',', ' ') }} FCFA</td>
                        <td>
                            @if($procedure->active)
                                <span class="badge-status validee">Active</span>
                            @else
                                <span class="badge-status rejetee">Inactive</span>
                            @endif
                        </td>
                        <td>{{ $procedure->created_at->format('d/m/Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">
                            Aucune démarche liée à ce ministère.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>
@endsection