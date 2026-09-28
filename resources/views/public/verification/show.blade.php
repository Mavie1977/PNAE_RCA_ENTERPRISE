@extends('layouts.app')

@section('title', 'Vérification du document officiel')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Vérification d’authenticité</h1>

        <p>
            Contrôle public d’un document officiel PNAE-RCA.
        </p>
    </div>

    <div class="verification-card">

        @php
    $documentAuthentic =
        $officialDocument->isActive()
        && $fileExists
        && $hashValid;
@endphp

@if($documentAuthentic)
    <div class="verification-result verification-valid">
        <span>✅</span>

        <div>
            <h2>Document authentique</h2>

            <p>
                Le document est enregistré, actif et son empreinte
                numérique correspond au fichier officiel conservé
                par la plateforme.
            </p>
        </div>
    </div>
@else
    <div class="verification-result verification-invalid">
        <span>⛔</span>

        <div>
            <h2>Document non valide</h2>

            <p>
                Le document est révoqué, introuvable ou son intégrité
                numérique ne peut pas être confirmée.
            </p>
        </div>
    </div>
@endif

        <dl class="verification-details">
            <div>
                <dt>Numéro officiel</dt>
                <dd>{{ $officialDocument->official_number }}</dd>
            </div>

            <div>
                <dt>Type de document</dt>
                <dd>{{ $officialDocument->title }}</dd>
            </div>

            <div>
                <dt>Bénéficiaire</dt>
                <dd>
                    {{ $officialDocument->application->user->name ?? '-' }}
                </dd>
            </div>

            <div>
                <dt>Démarche</dt>
                <dd>
                    {{ $officialDocument->application->procedure->title ?? '-' }}
                </dd>
            </div>

            <div>
                <dt>Ministère</dt>
                <dd>
                    {{ $officialDocument->application
                        ->procedure
                        ->ministry
                        ->name ?? '-' }}
                </dd>
            </div>

            <div>
                <dt>Date de délivrance</dt>
                <dd>
                    {{ $officialDocument->issued_at->format('d/m/Y H:i') }}
                </dd>
            </div>

            <div>
                <dt>Statut</dt>
                <dd>
                    <x-status-badge
                        :status="$officialDocument->status"
                        :label="$officialDocument->status_label"
                    />
                </dd>
            </div>

            <div>
                <dt>Intégrité du fichier</dt>
                <dd>
                    {{ $hashValid ? 'Conforme' : 'Non conforme' }}
                </dd>
            </div>
        </dl>

    </div>

</section>
@endsection