@extends('layouts.responsable')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">Demandes de mon ministère</h4>
        </div>

        <div class="card-body">
            <form method="GET"
                  action="{{ route('responsable.applications.index') }}"
                  class="row g-2 mb-4">
                <div class="col-md-5">
                    <select name="status" class="form-select">
                        <option value="">Tous les statuts</option>
                        @foreach ([
                            'soumise' => 'Soumise',
                            'en_traitement' => 'En traitement',
                            'validee' => 'Validée',
                            'rejetee' => 'Rejetée',
                            'terminee' => 'Terminée'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                @selected(request('status') === $value)>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-primary">
                        Filtrer
                    </button>
                </div>
            </form>

            <div class="table-responsive">
                <table class="table table-striped table-hover
                              table-bordered align-middle shadow-sm">
                    <thead class="table-primary">
                        <tr>
                            <th>Référence</th>
                            <th>Citoyen</th>
                            <th>Démarche</th>
                            <th>Statut</th>
                            <th>Agent affecté</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($applications as $application)
                            <tr>
                                <td>{{ $application->reference }}</td>
                                <td>{{ $application->user?->name ?? '—' }}</td>
                                <td>{{ $application->procedure?->title ?? '—' }}</td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                    </span>
                                </td>
                                <td>{{ $application->assignedAgent?->name ?? 'Non affectée' }}</td>
                                <td>
                                    <a href="{{ route('responsable.applications.show', $application) }}"
                                       class="btn btn-sm btn-outline-primary">
                                        Voir / Affecter
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4">
                                    Aucune demande trouvée pour ce ministère.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{ $applications->links() }}
        </div>
    </div>
</div>
@endsection