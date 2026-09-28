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

<x-panel
    title="Document officiel"
    subtitle="Générez le document final après validation du dossier."
    icon="📜"
>
    @if($application->officialDocument)

        <x-alert type="success" title="Document déjà généré">
            Numéro officiel :
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
            icon="⬇️"
        >
            Télécharger le document officiel
        </x-action-button>

    @else

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
                icon="📜"
            >
                Générer le document officiel
            </x-action-button>
        </form>

    @endif
</x-panel>


    <div class="dashboard-card mb-4">
        <h2>Informations générales</h2>

        <table class="rca-table mt-3">
            <tbody>
                <tr>
                    <th>Référence</th>
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
                    <th>Téléphone</th>
                    <td>{{ $application->user->phone ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Démarche</th>
                    <td>{{ $application->procedure->title ?? '-' }}</td>
                </tr>

                <tr>
                    <th>Ministère</th>
                    <td>
                        {{ $application->procedure->ministry->name ?? '-' }}
                    </td>
                </tr>

                <tr>
                    <th>Priorité</th>
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
                    <th>Date de dépôt</th>
                    <td>
                        {{ $application->created_at->format('d/m/Y H:i') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

   <x-panel
    title="Contrôle des pièces jointes"
    subtitle="Consultez, validez ou rejetez chaque document."
    icon="📎"
>
    <div class="document-review-grid">

        @forelse($application->documents as $document)

            <article class="document-review-card">

                <div class="document-review-header">

                    <div class="document-file-icon">
                        @if($document->mime_type === 'application/pdf')
                            📄
                        @elseif(str_starts_with((string) $document->mime_type, 'image/'))
                            🖼️
                        @else
                            📎
                        @endif
                    </div>

                    <div class="document-review-title">
                        <strong>
                            {{ $document->display_name }}
                        </strong>

                        <small>
                            {{ $document->mime_type ?? 'Type inconnu' }}
                            —
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
                        <strong>Note de contrôle :</strong>
                        <p>{{ $document->note }}</p>
                    </div>
                @endif

                <div class="document-review-actions">

                    <x-action-button
                        :href="route('agent.documents.show', $document)"
                        target="_blank"
                        variant="secondary"
                        size="small"
                        icon="👁️"
                    >
                        Aperçu
                    </x-action-button>

                    <x-action-button
                        :href="route('agent.documents.download', $document)"
                        variant="light"
                        size="small"
                        icon="⬇️"
                    >
                        Télécharger
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
                                Décision documentaire
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
                                    Reçu
                                </option>

                                <option
                                    value="valide"
                                    @selected(old('status', $document->status) === 'valide')
                                >
                                    Validé
                                </option>

                                <option
                                    value="rejete"
                                    @selected(old('status', $document->status) === 'rejete')
                                >
                                    Rejeté
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
                            💾 Enregistrer le contrôle du document
                        </button>
                    </div>

                </form>

            </article>

        @empty

            <x-empty-state
                icon="📭"
                title="Aucune pièce jointe"
                message="Le citoyen n’a joint aucun document à cette demande."
            />

        @endforelse

    </div>
</x-panel>

    <div class="form-card mb-4">

    <div class="form-header">
        <h2>Traitement administratif</h2>

        <p>
            Modifiez le statut général du dossier et ajoutez un commentaire.
        </p>
    </div>

    {{-- FORMULAIRE DU DOSSIER UNIQUEMENT --}}
    <form
        method="POST"
        action="{{ route('agent.applications.status', $application) }}"
    >
        @csrf
        @method('PATCH')

        <div class="form-grid">

            <div class="form-group">
                <label for="application-status">
                    Nouveau statut du dossier
                </label>

                @if(!empty($availableTransitions))

                    <select
                        id="application-status"
                        name="status"
                        required
                    >
                        <option value="">
                            Sélectionner le nouveau statut
                        </option>

                        @foreach($availableTransitions as $status => $label)
                            <option
                                value="{{ $status }}"
                                @selected(old('status') === $status)
                            >
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>

                @else

                    <div class="workflow-closed-message">
                        <strong>Dossier terminé</strong>

                        <span>
                            Aucune nouvelle transition n’est disponible.
                        </span>
                    </div>

                @endif
            </div>

            <div class="form-group">
                <label for="application-comment">
                    Commentaire agent
                </label>

                <textarea
                    id="application-comment"
                    name="comment"
                    rows="5"
                    placeholder="Précisez la décision ou les pièces manquantes..."
                >{{ old('comment') }}</textarea>
            </div>

        </div>

        <div class="form-actions">

            @if(!empty($availableTransitions))
                <button type="submit" class="btn-rca-primary">
                    Enregistrer le traitement du dossier
                </button>
            @endif

            <a
                href="{{ route('agent.applications') }}"
                class="btn-rca-secondary"
            >
                Retour à la liste
            </a>

        </div>

    </form>
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