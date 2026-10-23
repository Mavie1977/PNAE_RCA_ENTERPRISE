
@extends('layouts.app')

@section('title', 'Vérification du document officiel')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Vérification d'authenticité</h1>
        <p>Contrôle public d'un document officiel PNAE-RCA.</p>
    </div>

    <div class="verification-card">

        @if($documentAuthentic)
            <div class="verification-result verification-valid">
                <span>✓</span>

                <div>
                    <h2>Document authentifié et intègre</h2>

                    <p>
                        Le document est actif, son empreinte SHA-256
                        est conforme et sa signature applicative
                        HMAC V2 est valide.
                    </p>
                </div>
            </div>

        @elseif(
            $signatureVersion === null
            && $officialDocument->isActive()
            && $fileExists
            && $hashValid
        )
            <div class="verification-result verification-valid">
                <span>✓</span>

                <div>
                    <h2>Document historique enregistré et intègre</h2>

                    <p>
                        Le document est actif et son empreinte
                        SHA-256 correspond au fichier conservé
                        par la plateforme.
                    </p>

                    <p>
                        Sa signature applicative historique ne peut
                        pas être vérifiée avec HMAC V2.
                    </p>
                </div>
            </div>

        @else
            <div class="verification-result verification-invalid">
                <span>!</span>

                <div>
                    <h2>Vérification non concluante</h2>

                    <p>
                        Le document est révoqué, introuvable,
                        son intégrité est non conforme ou sa
                        signature applicative ne peut pas
                        être validée.
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
                <dt>Démarche</dt>
                <dd>
                    {{ $officialDocument->application?->procedure?->title ?? '-' }}
                </dd>
            </div>

            <div>
                <dt>Ministère compétent</dt>
                <dd>
                    {{ $officialDocument->application?->procedure?->ministry?->name ?? '-' }}
                </dd>
            </div>

            <div>
                <dt>Date de délivrance</dt>
                <dd>
                    {{ $officialDocument->issued_at?->format('d/m/Y H:i') ?? '-' }}
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
                    {{ $hashValid ? 'Conforme' : 'Non confirmée' }}
                </dd>
            </div>

            <div>
                <dt>Signature applicative</dt>
                <dd>
                    @if($signatureValid === true)
                        HMAC V2 — Conforme
                    @elseif($signatureValid === false)
                        Signature invalide ou non vérifiable
                    @else
                        Signature historique —
                        Vérification HMAC indisponible
                    @endif
                </dd>
            </div>

        </dl>

    </div>

</section>
@endsection
