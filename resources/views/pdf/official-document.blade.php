<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <title>{{ $officialDocument->title }}</title>

    <style>
        @page {
            margin: 28px 34px 32px;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            color: #152238;
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            line-height: 1.45;
        }

        .official-document {
            min-height: 1000px;
            position: relative;
            padding: 24px 26px;
            overflow: hidden;
            border: 3px solid #073b88;
        }

        /*
         * Filigrane de sécurité.
         * Dompdf gère mieux une couleur très claire qu'une opacité CSS.
         */
        .watermark {
            position: fixed;
            top: 390px;
            left: 70px;
            width: 610px;
            color: #f1f4fa;
            font-size: 57px;
            font-weight: bold;
            line-height: 1.25;
            text-align: center;
            transform: rotate(-32deg);
            z-index: -1000;
        }

        .watermark span {
            display: block;
            margin-top: 12px;
            font-size: 27px;
            letter-spacing: 5px;
        }

        /*
         * En-tête
         */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-logo {
            width: 24%;
        }

        .header-republic {
            width: 52%;
            text-align: center;
        }

        .header-platform {
            width: 24%;
            color: #073b88;
            font-size: 10px;
            line-height: 1.55;
            text-align: right;
        }

        .logo-rca {
            width: 92px;
            height: auto;
            display: block;
        }

        .republic-name {
            margin-top: 4px;
            color: #073b88;
            font-size: 15px;
            font-weight: bold;
            letter-spacing: 0.3px;
        }

        .republic-motto {
            margin-top: 5px;
            color: #26364f;
            font-size: 9px;
        }

        .platform-name {
            font-weight: bold;
        }

        /*
         * Titre
         */
        .title-block {
            margin-top: 42px;
            text-align: center;
        }

        .title-block h1 {
            margin: 0;
            color: #073b88;
            font-size: 25px;
            line-height: 1.25;
            text-transform: uppercase;
        }

        .official-number {
            margin-top: 10px;
            color: #d21034;
            font-size: 11px;
            font-weight: bold;
        }

        /*
         * Contenu
         */
        .introduction {
            margin-top: 45px;
            font-size: 13px;
            line-height: 1.7;
            text-align: justify;
        }

        .identity-table {
            width: 100%;
            margin-top: 22px;
            border-collapse: collapse;
        }

        .identity-table th,
        .identity-table td {
            padding: 10px 12px;
            border: 1px solid #cbd6e6;
        }

        .identity-table th {
            width: 34%;
            color: #073b88;
            font-weight: bold;
            text-align: left;
            background: #eaf1fc;
        }

        .identity-table td {
            width: 66%;
            background: #ffffff;
        }

        .certification {
            margin-top: 25px;
            padding: 15px 17px;
            color: #28384f;
            line-height: 1.65;
            background: #f7f9fc;
            border-left: 5px solid #ffcd00;
        }

        .issue-location {
            margin-top: 25px;
            font-size: 11px;
            text-align: right;
        }

        /*
         * Signature, cachet et QR Code
         */
        .authentication-section {
            margin-top: 30px;
        }

        .authentication-table {
            width: 100%;
            border-collapse: collapse;
        }

        .authentication-table td {
            vertical-align: top;
        }

        .signature-column {
            width: 58%;
            padding-right: 25px;
            text-align: center;
        }

        .qr-column {
            width: 42%;
            text-align: center;
        }

        .signatory-title {
            min-height: 35px;
            color: #152238;
            font-size: 12px;
            font-weight: bold;
        }

        .stamp {
            width: 105px;
            height: 105px;
            margin: 16px auto 13px;
            display: table;
            color: #073b88;
            border: 3px solid #073b88;
            border-radius: 50%;
            text-align: center;
        }

        .stamp-inner {
            display: table-cell;
            padding: 8px;
            vertical-align: middle;
            font-size: 8px;
            font-weight: bold;
            line-height: 1.45;
            text-transform: uppercase;
        }

        .stamp-separator {
            width: 50px;
            margin: 4px auto;
            border-top: 1px solid #073b88;
        }

        .signatory-name {
            margin-top: 8px;
            color: #152238;
            font-size: 12px;
            font-weight: bold;
        }

        .signatory-institution {
            margin-top: 4px;
            color: #536176;
            font-size: 9px;
        }

        .qr-title {
            margin-bottom: 9px;
            color: #073b88;
            font-size: 12px;
            font-weight: bold;
        }

        .qr-image {
            width: 145px;
            height: 145px;
        }

        .qr-instruction {
            width: 190px;
            margin: 8px auto 0;
            color: #4f5d72;
            font-size: 8px;
            line-height: 1.45;
        }

        /*
         * Bloc de sécurité
         */
        .security-block {
            margin-top: 24px;
            padding: 12px 14px;
            color: #073b88;
            font-size: 8px;
            background: #edf3ff;
            border: 1px solid #bccce2;
        }

        .security-title {
            margin-bottom: 6px;
            font-weight: bold;
            text-transform: uppercase;
        }

        .security-row {
            margin-top: 4px;
        }

        .security-label {
            font-weight: bold;
        }

        .security-value {
            color: #33435a;
            font-family: DejaVu Sans Mono, monospace;
            font-size: 7px;
            word-break: break-all;
        }

        /*
         * Bande de microtexte anti-copie
         */
        .microtext {
            margin-top: 10px;
            padding-top: 7px;
            overflow: hidden;
            color: #8b96a8;
            font-size: 5px;
            line-height: 1.2;
            text-align: justify;
            border-top: 1px solid #d5dce7;
        }

        .footer {
            position: fixed;
            right: 34px;
            bottom: 10px;
            left: 34px;
            color: #7a8698;
            font-size: 7px;
            text-align: center;
        }

        .footer-number {
            color: #073b88;
            font-weight: bold;
        }
    </style>
</head>

<body>

<div class="watermark">
    PNAE-RCA
    <span>DOCUMENT OFFICIEL</span>
</div>

<div class="official-document">

    {{-- En-tête institutionnel --}}
    <table class="header-table">
        <tr>
            <td class="header-logo">
                @if(!empty($logoDataUri))
                    <img
                        src="{{ $logoDataUri }}"
                        class="logo-rca"
                        alt="Armoiries de la République centrafricaine"
                    >
                @endif
            </td>

            <td class="header-republic">
                <div class="republic-name">
                    RÉPUBLIQUE CENTRAFRICAINE
                </div>

                <div class="republic-motto">
                    Unité — Dignité — Travail
                </div>
            </td>

            <td class="header-platform">
                <span class="platform-name">PNAE-RCA</span><br>
                Administration<br>
                électronique
            </td>
        </tr>
    </table>

    {{-- Titre du document --}}
    <div class="title-block">
        <h1>{{ $officialDocument->title }}</h1>

        <div class="official-number">
            N° {{ $officialDocument->official_number }}
        </div>
    </div>

    {{-- Introduction --}}
    <div class="introduction">
        La Plateforme Nationale d’Administration Électronique certifie
        que le présent document officiel a été délivré à :
    </div>

    {{-- Informations certifiées --}}
    <table class="identity-table">
        <tr>
            <th>Nom du bénéficiaire</th>
            <td>{{ $application->user->name ?? '-' }}</td>
        </tr>

        <tr>
            <th>Adresse électronique</th>
            <td>{{ $application->user->email ?? '-' }}</td>
        </tr>

        <tr>
            <th>Démarche administrative</th>
            <td>{{ $application->procedure->title ?? '-' }}</td>
        </tr>

        <tr>
            <th>Ministère compétent</th>
            <td>
                {{ $application->procedure->ministry->name ?? '-' }}
            </td>
        </tr>

        <tr>
            <th>Référence du dossier</th>
            <td>{{ $application->reference }}</td>
        </tr>

        <tr>
            <th>Date de délivrance</th>
            <td>
                {{ $officialDocument->issued_at->format('d/m/Y à H:i') }}
            </td>
        </tr>

        <tr>
            <th>Statut du document</th>
            <td>
                {{ $officialDocument->status_label }}
            </td>
        </tr>
    </table>

    {{-- Déclaration de conformité --}}
    <div class="certification">
        Après vérification du dossier, des pièces justificatives et,
        lorsque la démarche est payante, de la confirmation du paiement,
        le présent document est déclaré conforme aux informations
        enregistrées dans la Plateforme Nationale d’Administration
        Électronique.
    </div>

    <div class="issue-location">
        Fait à {{ $documentLocation }},
        le {{ $officialDocument->issued_at->format('d/m/Y') }}
    </div>

    {{-- Une seule zone de signature et un seul QR Code --}}
    <div class="authentication-section">
        <table class="authentication-table">
            <tr>
                <td class="signature-column">

                    <div class="signatory-title">
                        {{ $signatoryTitle }}
                    </div>

                    <div class="stamp">
                        <div class="stamp-inner">
                            République<br>
                            Centrafricaine

                            <div class="stamp-separator"></div>

                            Document officiel

                            <div class="stamp-separator"></div>

                            PNAE-RCA
                        </div>
                    </div>

                    <div class="signatory-name">
                        {{ $signatoryName }}
                    </div>

                    <div class="signatory-institution">
                        {{ $signatoryInstitution }}
                    </div>

                </td>

                <td class="qr-column">

                    <div class="qr-title">
                        Vérification d’authenticité
                    </div>

                    <img
                        src="{{ $qrCodeDataUri }}"
                        class="qr-image"
                        alt="QR Code de vérification"
                    >

                    <div class="qr-instruction">
                        Scannez ce QR Code pour contrôler l’authenticité,
                        l’intégrité et le statut du document.
                    </div>

                </td>
            </tr>
        </table>
    </div>

    {{-- Informations techniques de sécurité --}}
    <div class="security-block">
        <div class="security-title">
            Sécurité et intégrité numérique
        </div>

        <div class="security-row">
            <span class="security-label">
                Numéro officiel :
            </span>

            <span class="security-value">
                {{ $officialDocument->official_number }}
            </span>
        </div>

        <div class="security-row">
            <span class="security-label">
                Empreinte numérique SHA-256 :
            </span>

            <span class="security-value">
                {{ $officialDocument->hash_sha256 }}
            </span>
        </div>

        <div class="security-row">
            <span class="security-label">
                Signature électronique applicative :
            </span>

            <span class="security-value">
                {{ $officialDocument->signature_code }}
            </span>
        </div>
    </div>

    {{-- Microtexte anti-reproduction --}}
    <div class="microtext">
        PNAE-RCA • DOCUMENT OFFICIEL • RÉPUBLIQUE CENTRAFRICAINE •
        {{ $officialDocument->official_number }} •
        PNAE-RCA • DOCUMENT OFFICIEL • RÉPUBLIQUE CENTRAFRICAINE •
        {{ $officialDocument->official_number }} •
        Toute modification du présent fichier entraîne une différence
        avec l’empreinte numérique enregistrée dans la plateforme nationale.
    </div>

</div>

<div class="footer">
    Document généré électroniquement par la Plateforme Nationale
    d’Administration Électronique —
    <span class="footer-number">
        {{ $officialDocument->official_number }}
    </span>
</div>

</body>
</html>