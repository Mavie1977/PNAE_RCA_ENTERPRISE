@extends('layouts.responsable')

@section('title', 'Dossier RH Agent')

@section('content')

@php
    $profile = $agent->agentProfile;

    $statusLabel = match($profile?->administrative_status) {
        'active' => 'En activité',
        'secondment' => 'Détachement',
        'available' => 'Disponibilité',
        'suspended' => 'Suspendu',
        'retired' => 'Retraité',
        'dismissed' => 'Radié',
        default => 'Non défini',
    };

    $statusClass = match($profile?->administrative_status) {
        'active' => 'status-success',
        'secondment' => 'status-info',
        'available' => 'status-warning',
        'suspended' => 'status-danger',
        'retired' => 'status-neutral',
        'dismissed' => 'status-danger',
        default => 'status-neutral',
    };
@endphp

<style>
    .rh-page {
        max-width: 1180px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .rh-page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        gap: 24px;
        margin-bottom: 22px;
    }

    .rh-kicker {
        display: block;
        margin-bottom: 8px;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .12em;
        text-transform: uppercase;
        color: #d6002f;
    }

    .rh-page-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 31px;
        line-height: 1.15;
    }

    .rh-page-header p {
        margin: 8px 0 0;
        color: #61708a;
        font-size: 14px;
    }

    .rh-header-actions {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-end;
        gap: 9px;
    }

    .rh-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 7px;
        min-height: 38px;
        padding: 9px 15px;
        border: 0;
        border-radius: 8px;
        font-size: 13px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
        transition: .18s ease;
    }

    .rh-btn:hover {
        transform: translateY(-1px);
        text-decoration: none;
    }

    .rh-btn-primary {
        background: #073f91;
        color: #fff;
    }

    .rh-btn-yellow {
        background: #ffc900;
        color: #0d2753;
    }

    .rh-btn-light {
        background: #fff;
        color: #073f91;
        border: 1px solid #cad8eb;
    }

    .rh-summary {
        display: grid;
        grid-template-columns: 1.3fr repeat(3, 1fr);
        gap: 12px;
        margin-bottom: 18px;
    }

    .rh-summary-card {
        background: #fff;
        border: 1px solid #d8e2ef;
        border-radius: 12px;
        padding: 16px 18px;
        box-shadow: 0 5px 16px rgba(23, 57, 103, .04);
    }

    .rh-summary-card.featured {
        background: linear-gradient(135deg, #073f91, #145daf);
        color: #fff;
        border-color: transparent;
    }

    .rh-summary-label {
        display: block;
        margin-bottom: 6px;
        color: #748198;
        font-size: 11px;
        text-transform: uppercase;
        letter-spacing: .04em;
    }

    .featured .rh-summary-label {
        color: rgba(255,255,255,.75);
    }

    .rh-summary-value {
        color: #073b82;
        font-size: 17px;
        font-weight: 800;
    }

    .featured .rh-summary-value {
        color: #fff;
        font-size: 19px;
    }

    .rh-panel {
        margin-bottom: 16px;
        overflow: hidden;
        background: #fff;
        border: 1px solid #d7e1ee;
        border-radius: 13px;
        box-shadow: 0 5px 18px rgba(20, 54, 98, .04);
    }

    .rh-panel-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        padding: 17px 20px;
        border-bottom: 1px solid #e3eaf3;
    }

    .rh-panel-title-wrap {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .rh-panel-icon {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 38px;
        height: 38px;
        border-radius: 9px;
        background: #edf4ff;
        font-size: 18px;
    }

    .rh-panel-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 17px;
    }

    .rh-panel-header p {
        margin: 3px 0 0;
        color: #768397;
        font-size: 11px;
    }

    .rh-panel-body {
        padding: 18px 20px 20px;
    }

    .rh-grid {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
    }

    .rh-field {
        min-height: 82px;
        padding: 13px 14px;
        background: #f8fbff;
        border: 1px solid #dfe8f3;
        border-radius: 9px;
    }

    .rh-field-label {
        display: block;
        margin-bottom: 7px;
        color: #758298;
        font-size: 11px;
        font-weight: 600;
    }

    .rh-field-value {
        display: block;
        color: #063b81;
        font-size: 14px;
        font-weight: 800;
        line-height: 1.35;
        word-break: break-word;
    }

    .rh-status {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 10px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 800;
    }

    .status-success {
        background: #e3f7ec;
        color: #08783b;
    }

    .status-info {
        background: #e3f0ff;
        color: #07569f;
    }

    .status-warning {
        background: #fff3cd;
        color: #8a6500;
    }

    .status-danger {
        background: #fde7eb;
        color: #a20f2d;
    }

    .status-neutral {
        background: #edf0f4;
        color: #596273;
    }

    .rh-table-wrapper {
        overflow-x: auto;
    }

    .rh-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
    }

    .rh-table thead th {
        padding: 11px 10px;
        background: #094398;
        color: #fff;
        text-align: left;
        font-size: 10px;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .rh-table tbody td {
        padding: 12px 10px;
        border-bottom: 1px solid #e2e8f0;
        color: #263a59;
        vertical-align: top;
    }

    .rh-table tbody tr:last-child td {
        border-bottom: 0;
    }

    .rh-table tbody tr:hover {
        background: #f7faff;
    }

    .rh-empty {
        padding: 25px !important;
        text-align: center;
        color: #79869a !important;
    }

    .rh-history-title {
        font-weight: 800;
        color: #073b82;
    }

    .rh-history-type {
        display: block;
        margin-top: 3px;
        color: #8490a1;
        font-size: 10px;
    }

    .rh-notice {
        margin-top: 16px;
        padding: 12px 14px;
        border-left: 4px solid #0b55ac;
        border-radius: 7px;
        background: #eef5ff;
        color: #405574;
        font-size: 12px;
    }

    @media (max-width: 1100px) {
        .rh-summary,
        .rh-grid {
            grid-template-columns: repeat(2, minmax(0, 1fr));
        }
    }

    @media (max-width: 760px) {
        .rh-page-header {
            flex-direction: column;
        }

        .rh-header-actions {
            justify-content: flex-start;
        }

        .rh-summary,
        .rh-grid {
            grid-template-columns: 1fr;
        }

        .rh-page-header h1 {
            font-size: 25px;
        }
    }
	
	.rh-career-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 9px;
    border-radius: 999px;
    font-size: 10px;
    font-weight: 800;
}

.rh-career-advancement {
    background: #e8f1ff;
    color: #0756a8;
}

.rh-career-promotion {
    background: #e4f7eb;
    color: #08783b;
}

.rh-career-reclassification {
    background: #fff2cc;
    color: #865f00;
}

.rh-career-table {
    width: 100%;
    border-collapse: collapse;
}

.rh-career-table th {
    background: #0b4ca1;
    color: #fff;
    padding: 11px 12px;
    font-size: 11px;
    text-transform: uppercase;
    text-align: left;
}

.rh-career-table td {
    padding: 13px 12px;
    border-bottom: 1px solid #dbe5f1;
    vertical-align: middle;
    font-size: 12px;
    color: #082d63;
}

.rh-career-table tbody tr:hover {
    background: #f7faff;
}

.career-transition {
    display: flex;
    align-items: center;
    gap: 7px;
    white-space: nowrap;
}

.career-old {
    color: #64748b;
}

.career-arrow {
    color: #0b4ca1;
    font-weight: 900;
}

.career-new {
    color: #075eb5;
}

.career-badge {
    display: inline-flex;
    align-items: center;
    padding: 5px 9px;
    border-radius: 20px;
    font-size: 10px;
    font-weight: 800;
}

.career-advancement {
    background: #e7f0ff;
    color: #0757a6;
}

.career-promotion {
    background: #dcf7e6;
    color: #08783b;
}

.career-reclassification {
    background: #fff1c7;
    color: #805d00;
}

.career-reference {
    font-weight: 700;
    color: #0b4ca1;
}

.career-reason {
    margin-top: 4px;
    color: #64748b;
    font-size: 10px;
    line-height: 1.35;
}

.rh-empty {
    text-align: center;
    color: #64748b;
    padding: 25px !important;
}

.rh-leave-panel {
    margin-top: 14px;
}

.rh-leave-stats {
    display: grid;
    grid-template-columns: repeat(5, minmax(0, 1fr));
    gap: 12px;
    padding: 16px;
}

.rh-leave-stat {
    min-height: 72px;
    padding: 13px 14px;
    border: 1px solid #d9e3ef;
    border-radius: 9px;
    background: #f8fbff;

    display: flex;
    flex-direction: column;
    justify-content: center;
}

.rh-leave-label {
    margin-bottom: 6px;
    color: #6a7890;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .03em;
}

.rh-leave-value {
    color: #073b82;
    font-size: 21px;
    font-weight: 900;
}

.rh-leave-pending {
    color: #a56b00;
}

.rh-leave-approved {
    color: #08783b;
}

.rh-leave-rejected {
    color: #b4233b;
}

.rh-leave-stat-days {
    background: #0b4ca1;
    border-color: #0b4ca1;
}

.rh-leave-stat-days .rh-leave-label {
    color: #d8e7ff;
}

.rh-leave-stat-days .rh-leave-value {
    color: #fff;
}

@media (max-width: 950px) {
    .rh-leave-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 600px) {
    .rh-leave-stats {
        grid-template-columns: 1fr;
    }
}

.rh-document-stats {
    display: grid;
    grid-template-columns:
        minmax(0, 1fr)
        minmax(0, 1fr)
        minmax(0, 1fr)
        minmax(0, 2fr);
    gap: 12px;
    padding: 16px;
}

.rh-document-stat {
    min-height: 72px;
    padding: 13px 14px;
    background: #f8fbff;
    border: 1px solid #d9e3ef;
    border-radius: 9px;

    display: flex;
    flex-direction: column;
    justify-content: center;
}

.rh-document-label {
    margin-bottom: 6px;
    color: #6a7890;
    font-size: 10px;
    font-weight: 700;
    text-transform: uppercase;
}

.rh-document-value {
    color: #073b82;
    font-size: 21px;
    font-weight: 900;
}

.rh-document-active {
    color: #08783b;
}

.rh-document-archived {
    color: #64748b;
}

.rh-document-latest {
    background: #0b4ca1;
    border-color: #0b4ca1;
}

.rh-document-latest .rh-document-label {
    color: #d9e8ff;
}

.rh-document-latest-title {
    color: #fff;
    font-size: 12px;
    line-height: 1.3;
}

.rh-document-latest-date {
    margin-top: 5px;
    color: #d9e8ff;
    font-size: 10px;
}

@media (max-width: 900px) {
    .rh-document-stats {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }
}

@media (max-width: 550px) {
    .rh-document-stats {
        grid-template-columns: 1fr;
    }
}

</style>

<section class="rh-page">

    <header class="rh-page-header">
        <div>
            <span class="rh-kicker">
                Dossier ressources humaines
            </span>

            <h1>Dossier RH de {{ $agent->name }}</h1>

            <p>
                Situation administrative, affectation actuelle et principaux
                événements de carrière de l’agent public.
            </p>
        </div>

        <div class="rh-header-actions">

            @if (auth()->user()->isResponsableFonctionPublique())

                <a
                    href="{{ route(
                        'responsable.recruitment.agents.edit',
                        $agent
                    ) }}"
                    class="rh-btn rh-btn-primary"
                >
                    ✏ Modifier
                </a>

<a
    href="{{ route(
        'responsable.agents.leaves.index',
        $agent
    ) }}"
    class="rh-btn rh-btn-yellow"
>
    🗓 Congés / Absences
</a>


                <a
                    href="{{ route(
                        'responsable.recruitment.agents.transfer-form',
                        $agent
                    ) }}"
                    class="rh-btn rh-btn-yellow"
                >
                    🔄 Mutation
                </a>
				
				<a
    href="{{ route('responsable.agents.trainings.index', $agent) }}"
    class="rh-btn rh-btn-yellow"
>
    🎓 Formation
</a>

<a
    href="{{ route('responsable.agents.documents.index', $agent) }}"
    class="rh-btn rh-btn-yellow"
>
    📄 Documents RH
</a>


<a
    href="{{ route('responsable.agents.disciplinary.index', $agent) }}"
    class="rh-btn rh-btn-yellow"
>
    ⚖️ Discipline
</a>
            @endif

@if (auth()->user()->isResponsableFonctionPublique())

    <a
        href="{{ route('responsable.agents.advancement.create', $agent) }}"
        class="rh-btn rh-btn-yellow"
    >
        📈 Avancement
    </a>

@endif


<div class="rh-summary-card">
    <span class="rh-summary-label">
        Évolutions de carrière
    </span>

    <span class="rh-summary-value">
        {{ $advancements->count() }}
    </span>
</div>

            <a
                href="{{ route('responsable.agents.index') }}"
                class="rh-btn rh-btn-light"
            >
                ← Retour aux agents
            </a>

        </div>
    </header>


{{-- =========================================================
     CONGÉS ET ABSENCES
========================================================= --}}
<article class="rh-panel">

    <div class="rh-panel-header">
        <div class="rh-panel-title-wrap">
            <span class="rh-panel-icon">🗓️</span>

            <div>
                <h2>Congés et absences</h2>
                <p>
                    Synthèse des périodes d'absence enregistrées
                    dans le dossier RH.
                </p>
            </div>
        </div>

        <a
            href="{{ route('responsable.agents.leaves.index', $agent) }}"
            class="rh-btn rh-btn-yellow"
        >
            Voir les congés
        </a>
    </div>

    

<div class="rh-leave-stats">

        <div class="rh-leave-stat">
            <span class="rh-leave-label">
                Total enregistrés
            </span>

            <strong class="rh-leave-value">
                {{ $leaveStats['total'] ?? 0 }}
            </strong>
        </div>


        <div class="rh-leave-stat">
            <span class="rh-leave-label">
                En attente
            </span>

            <strong class="rh-leave-value rh-leave-pending">
                {{ $leaveStats['pending'] ?? 0 }}
            </strong>
        </div>


        <div class="rh-leave-stat">
            <span class="rh-leave-label">
                Validés
            </span>

            <strong class="rh-leave-value rh-leave-approved">
                {{ $leaveStats['approved'] ?? 0 }}
            </strong>
        </div>


        <div class="rh-leave-stat">
            <span class="rh-leave-label">
                Refusés
            </span>

            <strong class="rh-leave-value rh-leave-rejected">
                {{ $leaveStats['rejected'] ?? 0 }}
            </strong>
        </div>


        <div class="rh-leave-stat rh-leave-stat-days">
            <span class="rh-leave-label">
                Jours validés
            </span>

            <strong class="rh-leave-value">
                {{ $leaveStats['days_approved'] ?? 0 }}
            </strong>
        </div>

    </div>

</article>




{{-- =========================================================
     DISCIPLINE
========================================================= --}}
<article class="rh-panel">

    <div class="rh-panel-header">

        <div class="rh-panel-title-wrap">

            <span class="rh-panel-icon">⚖️</span>

            <div>
                <h2>Discipline</h2>

                <p>
                    Synthèse des procédures et sanctions disciplinaires.
                </p>
            </div>

        </div>

        <a
            href="{{ route('responsable.agents.disciplinary.index', $agent) }}"
            class="rh-btn rh-btn-yellow"
        >
            Voir les sanctions
        </a>

    </div>

    <div class="rh-leave-stats">

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Total</span>

            <strong class="rh-leave-value">
                {{ $disciplinaryStats['total'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">En attente</span>

            <strong class="rh-leave-value rh-leave-pending">
                {{ $disciplinaryStats['pending'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Validées</span>

            <strong class="rh-leave-value rh-leave-approved">
                {{ $disciplinaryStats['approved'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Refusées</span>

            <strong class="rh-leave-value rh-leave-rejected">
                {{ $disciplinaryStats['rejected'] ?? 0 }}
            </strong>
        </div>

    </div>

</article>
    

{{-- =========================================================
     DOCUMENTS ADMINISTRATIFS RH
========================================================= --}}
<article class="rh-panel">

    <div class="rh-panel-header">

        <div class="rh-panel-title-wrap">

            <span class="rh-panel-icon">📄</span>

            <div>
                <h2>Documents administratifs RH</h2>

                <p>
                    Pièces administratives rattachées au dossier de l’agent.
                </p>
            </div>

        </div>

        <a
            href="{{ route('responsable.agents.documents.index', $agent) }}"
            class="rh-btn rh-btn-yellow"
        >
            Voir les documents
        </a>

    </div>


    <div class="rh-document-stats">

        <div class="rh-document-stat">
            <span class="rh-document-label">
                Total
            </span>

            <strong class="rh-document-value">
                {{ $documentStats['total'] ?? 0 }}
            </strong>
        </div>


        <div class="rh-document-stat">
            <span class="rh-document-label">
                Actifs
            </span>

            <strong class="rh-document-value rh-document-active">
                {{ $documentStats['active'] ?? 0 }}
            </strong>
        </div>


        <div class="rh-document-stat">
            <span class="rh-document-label">
                Archivés
            </span>

            <strong class="rh-document-value rh-document-archived">
                {{ $documentStats['archived'] ?? 0 }}
            </strong>
        </div>


        <div class="rh-document-stat rh-document-latest">

            <span class="rh-document-label">
                Dernier document
            </span>

            @if($documentStats['latest'] ?? null)

                <strong class="rh-document-latest-title">
                    {{ $documentStats['latest']->title }}
                </strong>

                <span class="rh-document-latest-date">
                    {{ $documentStats['latest']->document_date?->format('d/m/Y')
                        ?? $documentStats['latest']->created_at?->format('d/m/Y')
                        ?? '—' }}
                </span>

            @else

                <strong class="rh-document-value">
                    Aucun
                </strong>

            @endif

        </div>

    </div>

</article>

<article class="rh-panel">

    <div class="rh-panel-header">

        <div class="rh-panel-title-wrap">

            <span class="rh-panel-icon">🎓</span>

            <div>
                <h2>Formation et compétences</h2>

                <p>
                    Parcours de formation, certifications et compétences acquises.
                </p>
            </div>

        </div>

        <a
            href="{{ route('responsable.agents.trainings.index', $agent) }}"
            class="rh-btn rh-btn-yellow"
        >
            Voir les formations
        </a>

    </div>


    <div class="rh-leave-stats">

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Formations</span>

            <strong class="rh-leave-value">
                {{ $trainingStats['total'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Planifiées</span>

            <strong class="rh-leave-value rh-leave-pending">
                {{ $trainingStats['planned'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">En cours</span>

            <strong class="rh-leave-value">
                {{ $trainingStats['in_progress'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Terminées</span>

            <strong class="rh-leave-value rh-leave-approved">
                {{ $trainingStats['completed'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Compétences</span>

            <strong class="rh-leave-value">
                {{ $trainingStats['skills'] ?? 0 }}
            </strong>
        </div>

        <div class="rh-leave-stat">
            <span class="rh-leave-label">Certifications</span>

            <strong class="rh-leave-value">
                {{ $trainingStats['certified'] ?? 0 }}
            </strong>
        </div>

    </div>

</article>



    <section class="rh-summary">

        <div class="rh-summary-card featured">
            <span class="rh-summary-label">
                Matricule national
            </span>

            <span class="rh-summary-value">
                {{ $profile?->matricule ?? 'Non attribué' }}
            </span>
        </div>

        <div class="rh-summary-card">
            <span class="rh-summary-label">
                Ministère actuel
            </span>

            <span class="rh-summary-value">
                {{ $profile?->ministry?->name
                    ?? $agent->ministry?->name
                    ?? 'Non affecté' }}
            </span>
        </div>

        <div class="rh-summary-card">
            <span class="rh-summary-label">
                Situation
            </span>

            <span class="rh-summary-value">
                {{ $statusLabel }}
            </span>
        </div>

        <div class="rh-summary-card">
            <span class="rh-summary-label">
                Compte applicatif
            </span>

            <span class="rh-summary-value">
                {{ $agent->active ? 'Actif' : 'Désactivé' }}
            </span>
        </div>

    </section>

    <article class="rh-panel">

        <div class="rh-panel-header">
            <div class="rh-panel-title-wrap">

                <span class="rh-panel-icon">🪪</span>

                <div>
                    <h2>Identité administrative</h2>
                    <p>Identification et coordonnées de l’agent.</p>
                </div>

            </div>
        </div>

        <div class="rh-panel-body">

            <div class="rh-grid">

                <div class="rh-field">
                    <span class="rh-field-label">Nom complet</span>
                    <span class="rh-field-value">
                        {{ $agent->name }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Matricule</span>
                    <span class="rh-field-value">
                        {{ $profile?->matricule ?? 'Non attribué' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Adresse électronique</span>
                    <span class="rh-field-value">
                        {{ $agent->email }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Téléphone</span>
                    <span class="rh-field-value">
                        {{ $agent->phone ?: 'Non renseigné' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Date de naissance</span>
                    <span class="rh-field-value">
                        {{ $profile?->birth_date?->format('d/m/Y')
                            ?? 'Non renseignée' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Lieu de naissance</span>
                    <span class="rh-field-value">
                        {{ $profile?->place_of_birth ?: 'Non renseigné' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Rôle applicatif</span>
                    <span class="rh-field-value">
                        Agent public
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">État du compte</span>

                    <span class="rh-status {{ $agent->active
                        ? 'status-success'
                        : 'status-danger' }}">
                        ● {{ $agent->active ? 'Actif' : 'Désactivé' }}
                    </span>
                </div>

            </div>

        </div>

    </article>

    <article class="rh-panel">

        <div class="rh-panel-header">
            <div class="rh-panel-title-wrap">

                <span class="rh-panel-icon">🏛️</span>

                <div>
                    <h2>Situation administrative</h2>
                    <p>Affectation et position actuelles dans l’administration.</p>
                </div>

            </div>
        </div>

        <div class="rh-panel-body">

            <div class="rh-grid">

                <div class="rh-field">
                    <span class="rh-field-label">Ministère d’affectation</span>
                    <span class="rh-field-value">
                        {{ $profile?->ministry?->name
                            ?? $agent->ministry?->name
                            ?? 'Non affecté' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Service</span>
                    <span class="rh-field-value">
                        {{ $profile?->serviceEntity?->name
                           ?? $profile?->getRawOriginal('service')
                           ?? 'Non renseigné' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Fonction</span>
                    <span class="rh-field-value">
                        {{ $profile?->hrFunction?->name
                           ?? $profile?->job_title
                           ?? 'Non renseignée' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Grade</span>
                    <span class="rh-field-value">
                        {{ $profile?->gradeEntity?->name
                           ?? $profile?->getRawOriginal('grade')
                           ?? 'Non renseigné' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Catégorie</span>
                    <span class="rh-field-value">
                        {{ $profile?->categoryEntity?->name
                           ?? $profile?->getRawOriginal('category')
                           ?? 'Non renseignée' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Position administrative</span>

                    <span class="rh-status {{ $statusClass }}">
                        ● {{ $statusLabel }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Ministry ID</span>
                    <span class="rh-field-value">
                        {{ $profile?->ministry_id
                            ?? $agent->ministry_id
                            ?? '—' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Observations RH</span>
                    <span class="rh-field-value">
                        {{ $profile?->notes ?: 'Aucune observation' }}
                    </span>
                </div>

            </div>

        </div>

    </article>

    <article class="rh-panel">

        <div class="rh-panel-header">
            <div class="rh-panel-title-wrap">

                <span class="rh-panel-icon">📄</span>

                <div>
                    <h2>Recrutement et prise de fonction</h2>
                    <p>Dates et références administratives principales.</p>
                </div>

            </div>
        </div>

        <div class="rh-panel-body">

            <div class="rh-grid">

                <div class="rh-field">
                    <span class="rh-field-label">Date de recrutement</span>
                    <span class="rh-field-value">
                        {{ $profile?->recruitment_date?->format('d/m/Y')
                            ?? 'Non renseignée' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Date de prise de fonction</span>
                    <span class="rh-field-value">
                        {{ $profile?->appointment_date?->format('d/m/Y')
                            ?? 'Non renseignée' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Référence de recrutement</span>
                    <span class="rh-field-value">
                        {{ $profile?->hire_reference ?: 'Non renseignée' }}
                    </span>
                </div>

                <div class="rh-field">
                    <span class="rh-field-label">Compte créé le</span>
                    <span class="rh-field-value">
                        {{ $agent->created_at?->format('d/m/Y H:i') ?? '—' }}
                    </span>
                </div>

            </div>

        </div>

    </article>

    <article class="rh-panel">

        <div class="rh-panel-header">

            <div class="rh-panel-title-wrap">

                <span class="rh-panel-icon">📚</span>

<article class="rh-panel">

    <div class="rh-panel-header">

        <div class="rh-panel-title-wrap">

            <span class="rh-panel-icon">📈</span>

            <div>
                <h2>Évolution de carrière</h2>

                <p>
                    Avancements, promotions et reclassements de l’agent.
                </p>
            </div>

        </div>

    </div>

    <div class="rh-panel-body">

        <div class="rh-table-wrapper">

            <table class="rh-table">

                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Type</th>
                        <th>Ancienne catégorie</th>
                        <th>Nouvelle catégorie</th>
                        <th>Ancien grade</th>
                        <th>Nouveau grade</th>
                        <th>Ancienne fonction</th>
                        <th>Nouvelle fonction</th>
                        <th>Référence</th>
                        <th>Auteur</th>
                    </tr>
                </thead>

                <tbody>

                    @forelse ($advancements as $advancement)

                        <tr>

                            <td>
                                {{ $advancement->effective_at?->format('d/m/Y') ?? '—' }}
                            </td>

                            <td>
                                @php
                                    $typeLabel = match($advancement->advancement_type) {
                                        'promotion' => 'Promotion',
                                        'reclassification' => 'Reclassement',
                                        default => 'Avancement',
                                    };
                                @endphp

                                @php
                             $typeClass = match($advancement->advancement_type) {
                                'promotion' => 'rh-career-promotion',
                                'reclassification' => 'rh-career-reclassification',
                                 default => 'rh-career-advancement',
                               };
                            @endphp

                           <span class="rh-career-badge {{ $typeClass }}">
                                     {{ $typeLabel }}
                          </span>
                            </td>

                            <td>
                                {{ $advancement->oldCategory?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $advancement->newCategory?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $advancement->oldGrade?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $advancement->newGrade?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $advancement->oldFunction?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $advancement->newFunction?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $advancement->reference ?: '—' }}
                            </td>

                            <td>
                                {{ $advancement->author?->name ?? 'Système' }}
                            </td>

                        </tr>

                    @empty

                        <tr>
                            <td colspan="10" class="rh-empty">
                                Aucun avancement, promotion ou reclassement enregistré.
                            </td>
                        </tr>

                    @endforelse

                </tbody>

            </table>

        </div>

    </div>

</article>
<div class="rh-table-wrapper">
    <table class="rh-table rh-career-table">
        <thead>
            <tr>
                <th>Date d'effet</th>
                <th>Opération</th>
                <th>Catégorie</th>
                <th>Grade</th>
                <th>Fonction</th>
                <th>Référence</th>
            </tr>
        </thead>

        <tbody>
            @forelse($advancements as $advancement)

                @php
                    $typeLabel = match($advancement->advancement_type) {
                        'promotion' => 'Promotion',
                        'reclassification' => 'Reclassement',
                        default => 'Avancement',
                    };

                    $typeClass = match($advancement->advancement_type) {
                        'promotion' => 'career-promotion',
                        'reclassification' => 'career-reclassification',
                        default => 'career-advancement',
                    };
                @endphp

                <tr>
                    <td>
                        <strong>
                            {{ $advancement->effective_at?->format('d/m/Y') ?? '—' }}
                        </strong>
                    </td>

                    <td>
                        <span class="career-badge {{ $typeClass }}">
                            {{ $typeLabel }}
                        </span>

                        @if($advancement->reason)
                            <div class="career-reason">
                                {{ $advancement->reason }}
                            </div>
                        @endif
                    </td>

                    <td>
                        <div class="career-transition">
                            <span class="career-old">
                                {{ $advancement->oldCategory?->name ?? '—' }}
                            </span>

                            <span class="career-arrow">→</span>

                            <strong>
                                {{ $advancement->newCategory?->name ?? '—' }}
                            </strong>
                        </div>
                    </td>

                    <td>
                        <div class="career-transition">
                            <span class="career-old">
                                {{ $advancement->oldGrade?->name ?? '—' }}
                            </span>

                            <span class="career-arrow">→</span>

                            <strong class="career-new">
                                {{ $advancement->newGrade?->name ?? '—' }}
                            </strong>
                        </div>
                    </td>

                    <td>
                        <div class="career-transition">
                            <span class="career-old">
                                {{ $advancement->oldFunction?->name ?? '—' }}
                            </span>

                            <span class="career-arrow">→</span>

                            <strong>
                                {{ $advancement->newFunction?->name ?? '—' }}
                            </strong>
                        </div>
                    </td>

                    <td>
                        <span class="career-reference">
                            {{ $advancement->reference ?: '—' }}
                        </span>
                    </td>
                </tr>

            @empty

                <tr>
                    <td colspan="6" class="rh-empty">
                        Aucun avancement, promotion ou reclassement enregistré.
                    </td>
                </tr>

            @endforelse
        </tbody>
    </table>
</div>

                <div>
                    <h2>Derniers événements de carrière</h2>
                    <p>Les dix derniers événements administratifs enregistrés.</p>
                </div>

            </div>

            @if (auth()->user()->isResponsableFonctionPublique())
                <a
                    href="{{ route(
                        'responsable.recruitment.agents.history',
                        $agent
                    ) }}"
                    class="rh-btn rh-btn-yellow"
                >
                    Historique complet
                </a>
            @endif

        </div>

        <div class="rh-panel-body">

            <div class="rh-table-wrapper">

                <table class="rh-table">

                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Événement</th>
                            <th>Ancienne affectation</th>
                            <th>Nouvelle affectation</th>
                            <th>Référence</th>
                            <th>Auteur</th>
                        </tr>
                    </thead>

                    <tbody>

                        @forelse ($histories as $history)

                            <tr>

                                <td>
                                    {{ $history->effective_at
                                        ?->format('d/m/Y H:i')
                                        ?? '—' }}
                                </td>

                                <td>
                                    <span class="rh-history-title">
                                        {{ $history->title }}
                                    </span>

                                    <span class="rh-history-type">
                                        {{ $history->event_type }}
                                    </span>
                                </td>

                                <td>
                                    {{ $history->fromMinistry?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $history->toMinistry?->name ?? '—' }}
                                </td>

                                <td>
                                    {{ $history->reference ?: '—' }}
                                </td>

                                <td>
                                    {{ $history->author?->name ?? 'Système' }}
                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="6" class="rh-empty">
                                    Aucun événement de carrière enregistré.
                                </td>
                            </tr>

                        @endforelse

                    </tbody>

                </table>

            </div>

            <div class="rh-notice">
                Le dossier RH constitue la synthèse administrative de l’agent.
                Les futures opérations de carrière seront ajoutées automatiquement
                à cet historique.
            </div>

        </div>

    </article>

</section>

@endsection