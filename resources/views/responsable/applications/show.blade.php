@extends('layouts.responsable')

@section('content')
<div class="container py-4">
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">
                Dossier {{ $application->reference }}
            </h4>
        </div>

        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <table class="table table-bordered table-striped shadow-sm">
                <tbody>
                    <tr>
                        <th style="width: 30%">Citoyen</th>
                        <td>{{ $application->user?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>DÃ©marche</th>
                        <td>{{ $application->procedure?->title ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>MinistÃ¨re</th>
                        <td>{{ $application->procedure?->ministry?->name ?? '—' }}</td>
                    </tr>
                    <tr>
                        <th>Statut</th>
                        <td>{{ ucfirst(str_replace('_', ' ', $application->status)) }}</td>
                    </tr>
                    <tr>
                        <th>Agent actuellement affectÃ©</th>
                        <td>{{ $application->assignedAgent?->name ?? 'Aucun' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Affectation Ã  un agent instructeur</h5>
        </div>

        <div class="card-body">
            @if (in_array($application->status, ['soumise', 'en_traitement'], true))
                <form method="POST"
                      action="{{ route('responsable.applications.assign', $application) }}">
                    @csrf
                    @method('PATCH')

                    <div class="mb-3">
                        <label for="agent_id" class="form-label">
                            Agent du ministÃ¨re
                        </label>
                        <select name="agent_id"
                                id="agent_id"
                                class="form-select"
                                required>
                            <option value="">SÃ©lectionner un agent</option>

                            @foreach ($agents as $agent)
                                <option value="{{ $agent->id }}"
                                    @selected(old('agent_id', $application->assigned_to) == $agent->id)>
                                    {{ $agent->name }} â€” {{ $agent->email }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit"
                            class="btn btn-primary"
                            @disabled($agents->isEmpty())>
                        Enregistrer l'affectation
                    </button>
                </form>
            @else
                <div class="alert alert-info mb-0">
                    L'affectation n'est pas autorisÃ©e pour ce statut.
                </div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light"><h5 class="mb-0">Proposition et décision ministérielle</h5></div>
        <div class="card-body">
            @if($pendingProposal && $application->status === 'en_traitement'
                && $application->assigned_to !== null
                && (int) $pendingProposal->agent_id === (int) $application->assigned_to)
                <p><strong>Agent :</strong> {{ $pendingProposal->agent?->name ?? '—' }}</p>
                <p><strong>Proposition :</strong> {{ $pendingProposal->proposed_status === 'validee' ? 'Validation du dossier' : 'Rejet du dossier' }}</p>
                <p><strong>Motivation :</strong> {{ $pendingProposal->comment }}</p>
                <form method="POST" action="{{ route('responsable.applications.decide', $application) }}">
                    @csrf
                    <input type="hidden" name="proposal_id" value="{{ $pendingProposal->id }}">
                    <div class="mb-3">
                        <label class="form-label" for="review_comment">Motivation du responsable</label>
                        <textarea class="form-control" name="review_comment" id="review_comment" maxlength="2000" rows="4" required>{{ old('review_comment') }}</textarea>
                    </div>
                    <button class="btn btn-success" type="submit" name="decision" value="approve">Adopter la proposition (décision finale)</button>
                    <button class="btn btn-outline-danger" type="submit" name="decision" value="reject">Refuser la proposition (retour à l'agent)</button>
                </form>
            @else
                <div class="alert alert-info mb-0">Aucune proposition valide en attente pour ce dossier.</div>
            @endif
        </div>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-light">
            <h5 class="mb-0">Historique du traitement</h5>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped shadow-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Intervenant</th>
                            <th>Ancien statut</th>
                            <th>Nouveau statut</th>
                            <th>Commentaire</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($application->workflowLogs as $log)
                            <tr>
                                <td>{{ $log->created_at?->format('d/m/Y H:i') }}</td>
                                <td>{{ $log->user?->name ?? 'Système' }}</td>
                                <td>{{ $log->from_status ?? '—' }}</td>
                                <td>{{ $log->to_status ?? '—' }}</td>
                                <td>{{ $log->comment ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center">
                                    Aucun Ã©vÃ©nement enregistrÃ©.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <a href="{{ route('responsable.applications.index') }}"
       class="btn btn-outline-secondary">
        Retour aux demandes
    </a>
</div>
@endsection
