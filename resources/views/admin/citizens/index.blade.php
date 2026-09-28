@extends('layouts.admin')

@section('title', 'Gestion des citoyens')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Gestion des citoyens</h1>
        <p>Consultez, activez ou désactivez les comptes citoyens.</p>
    </div>

    <div class="table-card">
        <table class="rca-table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Email</th>
                    <th>Téléphone</th>
                    <th>Demandes</th>
                    <th>Statut</th>
                    <th>Date création</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($citizens as $citizen)
                    <tr>
                        <td><strong>{{ $citizen->name }}</strong></td>
                        <td>{{ $citizen->email }}</td>
                        <td>{{ $citizen->phone ?? '-' }}</td>
                        <td>{{ $citizen->applications_count }}</td>
                        <td>
                            @if($citizen->active)
                                <span class="badge-status validee">Actif</span>
                            @else
                                <span class="badge-status rejetee">Inactif</span>
                            @endif
                        </td>
                        <td>{{ $citizen->created_at->format('d/m/Y') }}</td>
                        <td>
                            <a href="{{ route('admin.citizens.show', $citizen) }}" class="btn-table">
                                Voir
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center">Aucun citoyen enregistré.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</section>
@endsection