@extends('layouts.agent')

@section('title', 'Traitement du dossier')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Traitement du dossier</h1>
        <p>{{ $application->reference }}</p>
    </div>

    @if(session('success'))
        <div class="alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if(session('warning'))
        <div class="alert-warning mb-4">
            {{ session('warning') }}
        </div>
    @endif

    @if($errors->any())
        <div class="alert-error mb-4">
            <strong>Veuillez corriger les erreurs suivantes :</strong>

            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif


{{-- Prise en charge du dossier par l'agent instructeur --}}
@if($application->status === 'soumise')
    <div class="card shadow-sm border-0 my-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Prise en charge du dossier</h5>
        </div>

        <div class="card-body">
            <p>
                Ce dossier vous a été affecté.
                Vous pouvez commencer son instruction.
            </p>

            <form
                method="POST"
                action="{{ route('agent.applications.status', $application) }}"
            >
                @csrf
                @method('PATCH')

                <input
                    type="hidden"
                    name="status"
                    value="en_traitement"
                >

                <button type="submit" class="btn btn-primary">
                    Prendre en traitement
                </button>
            </form>
        </div>
    </div>
@endif
@if ($application->status === 'en_traitement')
    <div class="card shadow-sm border-0 my-4">
        <div class="card-header bg-primary text-white"><h5 class="mb-0">Proposer une décision</h5></div>
        <div class="card-body">
            @if($pendingProposal)
                <div class="alert alert-info">Une proposition est déjà en attente d'examen par le responsable.</div>
                <p><strong>Proposition :</strong> {{ $pendingProposal->proposed_status === 'validee' ? 'Validation' : 'Rejet' }}</p>
                <p>{{ $pendingProposal->comment }}</p>
            @else
                <form method="POST" action="{{ route('agent.applications.propose', $application) }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Décision proposée</label>
                        <select name="proposed_status" class="form-select" required>
                            <option value="">Sélectionner</option>
                            <option value="validee" @selected(old('proposed_status') === 'validee')>Proposer la validation</option>
                            <option value="rejetee" @selected(old('proposed_status') === 'rejetee')>Proposer le rejet</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Motivation</label>
                        <textarea name="comment" class="form-control" rows="4" maxlength="2000" required>{{ old('comment') }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Transmettre au responsable</button>
                </form>
            @endif
        </div>
    </div>
@endif

<x-panel
    title="Document officiel"
    subtitle="GÃ©nÃ©rez le document final aprÃ¨s validation du dossier."
    icon="ðŸ“œ"
>
    @if($application->officialDocument)

        <x-alert type="success" title="Document dÃ©jÃ  gÃ©nÃ©rÃ©">
            NumÃ©ro officiel :
            <strong>
                {{ $application->officialDocument->official_number }}
            </strong>
        </x-alert>

        <x-action-button
            :href="route(
                'agent.official-documents.download',
                $application->officialDocument
            )"
            variant="success"
            icon="â¬‡ï¸"
        >
            TÃ©lÃ©charger le document officiel
        </x-action-button>

    @elseif(in_array($application->status, ['validee', 'terminee'], true))

        <form
            method="POST"
            action="{{ route(
                'agent.official-documents.store',
                $application
            ) }}"
        >
            @csrf

            <x-action-button
                type="submit"
                variant="success"
                icon="ðŸ“œ"
            >
                GÃ©nÃ©rer le document officiel
            </x-action-button>
        </form>
    @else
        <p>La génération du document officiel sera disponible après validation du dossier.</p>
    @endif
</x-panel>


    <div class="dashboard-card mb-4">
        <h2>Informations gÃ©nÃ©rales</h2>

        <table class="rca-table mt-3">
            <tbody>
                <tr>
                    <th>RÃ©fÃ©rence</th>
                    <td>{{ $application->reference }}</td>
                </tr>

                <tr>
                    <th>Citoyen</th>
                    <td>{{ $application->user->name ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Email</th>
                    <td>{{ $application->user->email ?? '-' }}</td>
                </tr>

                <tr>
                    <th>TÃ©lÃ©phone</th>
                    <td>{{ $application->user->phone ?? '-' }}</td>
                </tr>

                <tr>
                    <th>DÃ©marche</th>
                    <td>{{ $application->procedure->title ?? '-' }}</td>
                </tr>

                <tr>
                    <th>MinistÃ¨re</th>
                    <td>
                        {{ $application->procedure->ministry->name ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th>PrioritÃ©</th>
                    <td>
                        {{ ucfirst($application->priority ?? 'normale') }}
                    </td>
                </tr>

                <tr>
                    <th>Paiement</th>
                    <td>
                        {{ ucfirst(str_replace('_', ' ', $application->payment_status ?? 'en_attente')) }}
                    </td>
                </tr>

                <tr>
                    <th>Statut</th>
                    <td>
                        <span class="badge-status {{ $application->status }}">
                            {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                        </span>
                    </td>
                </tr>

                <tr>
                    <th>Message du citoyen</th>
                    <td>
                        {!! nl2br(e($application->message ?? '-')) !!}
                    </td>
                </tr>

                <tr>
                    <th>Date de dÃ©pÃ´t</th>
                    <td>
                        {{ $application->created_at?->format('d/m/Y H:i') ?? 'Date non renseignée' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

   <x-panel
    title="ContrÃ´le des piÃ¨ces jointes"
    subtitle="Consultez, validez ou rejetez chaque document."
    icon="ðŸ“Ž"
>
    <div class="document-review-grid">

        @forelse($application->documents as $document)

            <article class="document-review-card">

                <div class="document-review-header">

                    <div class="document-file-icon">
                        @if($document->mime_type === 'application/pdf')
                            ðŸ“„
                        @elseif(str_starts_with((string) $document->mime_type, 'image/'))
                            ðŸ–¼ï¸
                        @else
                            ðŸ“Ž
                        @endif
                    </div>

                    <div class="document-review-title">
                        <strong>
                            {{ $document->display_name }}
                        </strong>

                        <small>
                            {{ $document->mime_type ?? 'Type inconnu' }}
                            â€”
                            {{ $document->formatted_size }}
                        </small>
                    </div>

                    <x-status-badge
                        :status="$document->status"
                        :label="$document->status_label"
                    />

                </div>

                @if($document->note)
                    <div class="document-review-note">
                        <strong>Note de contrÃ´le :</strong>
                        <p>{{ $document->note }}</p>
                    </div>
                @endif

                <div class="document-review-actions">

                    <x-action-button
                        :href="route('agent.documents.show', $document)"
                        target="_blank"
                        variant="secondary"
                        size="small"
                        icon="ðŸ‘ï¸"
                    >
                        AperÃ§u
                    </x-action-button>

                    <x-action-button
                        :href="route('agent.documents.download', $document)"
                        variant="light"
                        size="small"
                        icon="â¬‡ï¸"
                    >
                        TÃ©lÃ©charger
                    </x-action-button>

                </div>

                {{-- FORMULAIRE DU DOCUMENT UNIQUEMENT --}}
                <form
                    method="POST"
                    action="{{ route('agent.documents.status', $document) }}"
                    class="document-review-form"
                >
                    @csrf
                    @method('PATCH')

                    <div class="document-form-grid">

                        <div class="form-group">
                            <label for="document-status-{{ $document->id }}">
                                DÃ©cision documentaire
                            </label>

                            <select
                                id="document-status-{{ $document->id }}"
                                name="status"
                                required
                            >
                                <option
                                    value="attendu"
                                    @selected(old('status', $document->status) === 'attendu')
                                >
                                    Attendu
                                </option>

                                <option
                                    value="recu"
                                    @selected(old('status', $document->status) === 'recu')
                                >
                                    ReÃ§u
                                </option>

                                <option
                                    value="valide"
                                    @selected(old('status', $document->status) === 'valide')
                                >
                                    ValidÃ©
                                </option>

                                <option
                                    value="rejete"
                                    @selected(old('status', $document->status) === 'rejete')
                                >
                                    RejetÃ©
                                </option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="document-note-{{ $document->id }}">
                                Note ou motif du rejet
                            </label>

                            <textarea
                                id="document-note-{{ $document->id }}"
                                name="note"
                                rows="3"
                                placeholder="Observation ou motif du rejet..."
                            >{{ old('note', $document->note) }}</textarea>
                        </div>

                    </div>

                    <div class="document-form-actions">
                        <button
                            type="submit"
                            class="component-action-button button-success button-small"
                        >
                            ðŸ’¾ Enregistrer le contrÃ´le du document
                        </button>
                    </div>

                </form>

            </article>

        @empty

            <x-empty-state
                icon="ðŸ“­"
                title="Aucune piÃ¨ce jointe"
                message="Le citoyen nâ€™a joint aucun document Ã  cette demande."
            />

        @endforelse

    </div>
</x-panel>

    <div class="mb-4">
        <p class="text-muted">La décision finale appartient au responsable ministériel.</p>
        <a href="{{ route('agent.applications') }}" class="btn-rca-secondary">Retour à la liste</a>
    </div>

    <div class="table-card">
        <h2>Historique du dossier</h2>

        <div class="table-responsive mt-3">
            <table class="rca-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Utilisateur</th>
                        <th>Ancien statut</th>
                        <th>Nouveau statut</th>
                        <th>Commentaire</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($application->workflowLogs->sortByDesc('created_at') as $log)
                        <tr>
                            <td>
                                {{ $log->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                {{ $log->user->name ?? 'Système' }}
                            </td>

                            <td>
                                {{ ucfirst(str_replace('_', ' ', $log->from_status ?? '-')) }}
                            </td>

                            <td>
                                {{ ucfirst(str_replace('_', ' ', $log->to_status ?? '-')) }}
                            </td>

                            <td>
                                {{ $log->comment ?? '-' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Aucun historique de traitement.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>
@endsection
