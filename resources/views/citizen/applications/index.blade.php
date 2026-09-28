@extends('layouts.citizen')

@section('title', 'Mes demandes')

@section('content')

<section class="page-section">

    {{-- =====================================================
         EN-TÊTE
         ===================================================== --}}
    <div class="page-header">

        <div>
            <span class="page-kicker">ESPACE CITOYEN</span>

            <h1>Mes demandes</h1>

            <p>
                Consultez vos démarches, leurs statuts, leurs paiements
                et les pièces jointes déposées.
            </p>
        </div>

        <div class="page-header-actions">
            <a
                href="{{ route('citizen.application.create') }}"
                class="btn-rca-primary"
            >
                ➕ Nouvelle demande
            </a>
        </div>

    </div>

    {{-- =====================================================
         LISTE DES DEMANDES
         ===================================================== --}}
    <div class="table-card">

        <div class="table-card-header">
            <div>
                <h2>Liste des demandes</h2>

                <p>
                    Historique de vos demandes administratives.
                </p>
            </div>
        </div>

        <div class="table-responsive">

            <table class="rca-table citizen-applications-table">

                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Démarche</th>
                        <th>Ministère</th>
                        <th>Statut</th>
                        <th>Paiement</th>
                        <th>Date</th>
                        <th>Documents</th>
                        <th>Document officiel</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse($applications as $application)

                        @php
                            $procedureFee = (float) (
                                $application->procedure->fee ?? 0
                            );

                            $isPaid =
                                $application->payment_status === 'paye';

                            $hasDocuments =
                                $application->documents->isNotEmpty();

                            $officialDocument =
                                $application->officialDocument;
                        @endphp

                        {{-- =========================================
                             LIGNE PRINCIPALE DE LA DEMANDE
                             ========================================= --}}
                        <tr class="application-main-row">

                            {{-- Référence --}}
                            <td>
                                <strong class="application-reference">
                                    {{ $application->reference }}
                                </strong>
                            </td>

                            {{-- Démarche --}}
                            <td>
                                {{
                                    $application->procedure->title
                                    ?? 'Démarche administrative'
                                }}
                            </td>

                            {{-- Ministère --}}
                            <td>
                                {{
                                    $application->procedure
                                        ->ministry
                                        ->name
                                    ?? '-'
                                }}
                            </td>

                            {{-- Statut du dossier --}}
                            <td>
                                <x-status-badge
                                    :status="$application->status"
                                />
                            </td>

                            {{-- Paiement --}}
                            <td class="payment-cell">

                                @if($isPaid)

                                    <x-status-badge
                                        status="paye"
                                        label="Payé"
                                    />

                                    @if($application->latestPayment)
                                        <div class="application-payment-action">
                                            <a
                                                href="{{ route(
                                                    'citizen.payments.show',
                                                    $application->latestPayment
                                                ) }}"
                                                class="btn-table-secondary"
                                            >
                                                Voir le paiement
                                            </a>
                                        </div>
                                    @endif

                                @else

                                    <x-status-badge
                                        status="en_attente"
                                        label="En attente"
                                    />

                                    <div class="application-payment-action">
                                        <a
                                            href="{{ route(
                                                'citizen.payments.create',
                                                $application
                                            ) }}"
                                            class="btn-table-warning"
                                        >
                                            💳 Payer maintenant
                                        </a>
                                    </div>

                                    @if($procedureFee <= 0)
                                        <small class="payment-information">
                                            Montant à confirmer
                                        </small>
                                    @else
                                        <small class="payment-information">
                                            {{
                                                number_format(
                                                    $procedureFee,
                                                    0,
                                                    ',',
                                                    ' '
                                                )
                                            }}
                                            FCFA
                                        </small>
                                    @endif

                                @endif

                            </td>

                            {{-- Date --}}
                            <td>
                                {{
                                    $application->created_at
                                        ?->format('d/m/Y H:i')
                                }}
                            </td>

                            {{-- Nombre de documents --}}
                            <td>
                                @if($hasDocuments)

                                    <span class="documents-count">
                                        {{
                                            $application
                                                ->documents
                                                ->count()
                                        }}
                                        pièce(s)
                                    </span>

                                @else

                                    <span class="text-muted">
                                        Aucun document
                                    </span>

                                @endif
                            </td>

                            {{-- Document officiel --}}
                            <td>
                                @if($officialDocument)

                                    <a
                                        href="{{ route(
                                            'citizen.official-documents.download',
                                            $officialDocument
                                        ) }}"
                                        class="btn-table-success"
                                    >
                                        ⬇ Télécharger
                                    </a>

                                @elseif(
                                    in_array(
                                        $application->status,
                                        ['validee', 'terminee'],
                                        true
                                    )
                                )

                                    <span class="status-preparation">
                                        En préparation
                                    </span>

                                @else

                                    <span class="text-muted">
                                        Non disponible
                                    </span>

                                @endif
                            </td>

                        </tr>

                        {{-- =========================================
                             SOUS-TABLEAU DES PIÈCES JOINTES
                             ========================================= --}}
                        @if($hasDocuments)

                            <tr class="application-documents-row">

                                <td colspan="8">

                                    <div class="citizen-documents-block">

                                        <div class="citizen-documents-heading">

                                            <div>
                                                <h3>Pièces jointes</h3>

                                                <p>
                                                    Documents déposés pour la
                                                    demande
                                                    {{ $application->reference }}.
                                                </p>
                                            </div>

                                            <span class="documents-reference">
                                                {{
                                                    $application
                                                        ->documents
                                                        ->count()
                                                }}
                                                document(s)
                                            </span>

                                        </div>

                                        <div class="table-responsive">

                                            <table class="rca-table citizen-documents-table">

                                                <thead>
                                                    <tr>
                                                        <th>Nom du fichier</th>
                                                        <th>Type</th>
                                                        <th>Taille</th>
                                                        <th>Contrôle</th>
                                                        <th>Observation</th>
                                                        <th>Action</th>
                                                    </tr>
                                                </thead>

                                                <tbody>

                                                    @foreach(
                                                        $application->documents
                                                        as $document
                                                    )

                                                        <tr>

                                                            {{-- Nom --}}
                                                            <td>
                                                                <strong>
                                                                    {{
                                                                        $document
                                                                            ->display_name
                                                                    }}
                                                                </strong>
                                                            </td>

                                                            {{-- Type --}}
                                                            <td>
                                                                {{
                                                                    $document
                                                                        ->mime_type
                                                                    ?? '-'
                                                                }}
                                                            </td>

                                                            {{-- Taille --}}
                                                            <td>
                                                                {{
                                                                    $document
                                                                        ->formatted_size
                                                                }}
                                                            </td>

                                                            {{-- Contrôle --}}
                                                            <td>
                                                                <x-status-badge
                                                                    :status="$document->status"
                                                                    :label="$document->status_label"
                                                                />
                                                            </td>

                                                            {{-- Observation --}}
                                                            <td>
                                                                @if($document->note)

                                                                    <details class="citizen-document-note">

                                                                        <summary>
                                                                            Voir l’observation
                                                                        </summary>

                                                                        <p>
                                                                            {{
                                                                                $document
                                                                                    ->note
                                                                            }}
                                                                        </p>

                                                                    </details>

                                                                @else

                                                                    <span class="text-muted">
                                                                        Aucune observation
                                                                    </span>

                                                                @endif
                                                            </td>

                                                            {{-- Téléchargement --}}
                                                            <td>
                                                                <a
                                                                    href="{{ asset(
                                                                        'storage/'
                                                                        . $document->file_path
                                                                    ) }}"
                                                                    target="_blank"
                                                                    rel="noopener"
                                                                    class="btn-table-warning"
                                                                >
                                                                    ⬇ Télécharger
                                                                </a>
                                                            </td>

                                                        </tr>

                                                    @endforeach

                                                </tbody>

                                            </table>

                                        </div>

                                    </div>

                                </td>

                            </tr>

                        @endif

                    @empty

                        {{-- =========================================
                             AUCUNE DEMANDE
                             ========================================= --}}
                        <tr>

                            <td colspan="8">

                                <div class="empty-state">

                                    <div class="empty-state-icon">
                                        📭
                                    </div>

                                    <h3>Aucune demande</h3>

                                    <p>
                                        Vous n’avez encore déposé aucune
                                        demande administrative.
                                    </p>

                                    <a
                                        href="{{ route(
                                            'citizen.application.create'
                                        ) }}"
                                        class="btn-rca-primary"
                                    >
                                        ➕ Déposer une demande
                                    </a>

                                </div>

                            </td>

                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</section>

@endsection