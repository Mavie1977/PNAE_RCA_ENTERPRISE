@extends('layouts.admin')

@section('title', 'Consultation du dossier')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Consultation du dossier</h1>
        <p>{{ $application->reference }}</p>
    </div>

    <div class="dashboard-panel mb-4">
        <h2>Informations générales</h2>

        <div class="table-responsive mt-3">
            <table class="rca-table">
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
                        <th>Statut</th>
                        <td>
                            <span class="badge-status {{ $application->status }}">
                                {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Paiement</th>
                        <td>
                            {{ ucfirst(str_replace('_', ' ', $application->payment_status ?? 'en_attente')) }}
                        </td>
                    </tr>

                    <tr>
                        <th>Priorité</th>
                        <td>{{ ucfirst($application->priority ?? 'normale') }}</td>
                    </tr>

                    <tr>
                        <th>Agent affecté</th>
                        <td>{{ $application->assignedAgent->name ?? 'Non affecté' }}</td>
                    </tr>

                    <tr>
                        <th>Date de dépôt</th>
                        <td>{{ $application->created_at->format('d/m/Y H:i') }}</td>
                    </tr>

                    <tr>
                        <th>Message</th>
                        <td>{!! nl2br(e($application->message ?? '-')) !!}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="dashboard-panel mb-4">
        <h2>Pièces jointes</h2>

        <div class="table-responsive mt-3">
            <table class="rca-table">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Type</th>
                        <th>Taille</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($application->documents as $document)
                        <tr>
                            <td>
                                {{ $document->original_name ?? $document->label ?? 'Document' }}
                            </td>

                            <td>{{ $document->mime_type ?? '-' }}</td>

                            <td>
                                {{ $document->size
                                    ? number_format($document->size / 1024, 1, ',', ' ') . ' Ko'
                                    : '-'
                                }}
                            </td>

                            <td>
                                <a
                                    href="{{ asset('storage/' . $document->file_path) }}"
                                    target="_blank"
                                    rel="noopener"
                                    class="btn-table"
                                >
                                    Voir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                Aucun document.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="dashboard-panel">
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
                            <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ $log->user->name ?? 'Système' }}</td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $log->from_status ?? '-')) }}
                            </td>
                            <td>
                                {{ ucfirst(str_replace('_', ' ', $log->to_status ?? '-')) }}
                            </td>
                            <td>{{ $log->comment ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">
                                Aucun historique.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</section>
@endsection