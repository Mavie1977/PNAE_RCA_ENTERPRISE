@extends('layouts.responsable')

@section('title', 'Avancement / Promotion')

@section('content')

@php
    $profile = $agent->agentProfile;
@endphp

<style>
    .adv-page {
        max-width: 1080px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .adv-header {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .adv-kicker {
        display: block;
        margin-bottom: 7px;
        color: #d6002f;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .adv-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
    }

    .adv-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .adv-current {
        display: grid;
        grid-template-columns: repeat(4, minmax(0, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .adv-current-card {
        padding: 14px;
        background: #fff;
        border: 1px solid #d6e1ef;
        border-radius: 9px;
    }

    .adv-current-card span {
        display: block;
        margin-bottom: 5px;
        color: #77859a;
        font-size: 10px;
        text-transform: uppercase;
    }

    .adv-current-card strong {
        color: #073b82;
        font-size: 14px;
    }

    .adv-card {
        background: #fff;
        border: 1px solid #d7e1ee;
        border-radius: 13px;
        overflow: hidden;
    }

    .adv-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e4eaf2;
    }

    .adv-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 17px;
    }

    .adv-body {
        padding: 20px;
    }

    .adv-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .adv-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .adv-field.full {
        grid-column: 1 / -1;
    }

    .adv-field label {
        color: #17365f;
        font-size: 12px;
        font-weight: 800;
    }

    .adv-field input,
    .adv-field select,
    .adv-field textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #cad8e8;
        border-radius: 7px;
        box-sizing: border-box;
    }

    .adv-field textarea {
        min-height: 100px;
        resize: vertical;
    }

    .adv-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    .adv-btn {
        padding: 10px 18px;
        border: 0;
        border-radius: 8px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .adv-btn-primary {
        background: #073f91;
        color: #fff;
    }

    .adv-btn-yellow {
        background: #ffc900;
        color: #0b2e61;
    }

    @media (max-width: 760px) {
        .adv-current,
        .adv-grid {
            grid-template-columns: 1fr;
        }

        .adv-field.full {
            grid-column: auto;
        }
    }
</style>

<section class="adv-page">

    <header class="adv-header">
        <div>
            <span class="adv-kicker">
                Gestion de carrière
            </span>

            <h1>
                Avancement / Promotion
            </h1>

            <p>
                {{ $agent->name }}
                —
                {{ $profile?->matricule }}
            </p>
        </div>

        <a
            href="{{ route(
                'responsable.agents.show',
                $agent
            ) }}"
            class="adv-btn adv-btn-yellow"
        >
            Retour au dossier RH
        </a>
    </header>


    <div class="adv-current">

        <div class="adv-current-card">
            <span>Catégorie actuelle</span>

            <strong>
                {{ $profile?->categoryEntity?->name
                    ?? 'Non renseignée' }}
            </strong>
        </div>

        <div class="adv-current-card">
            <span>Grade actuel</span>

            <strong>
                {{ $profile?->gradeEntity?->name
                    ?? 'Non renseigné' }}
            </strong>
        </div>

        <div class="adv-current-card">
            <span>Fonction actuelle</span>

            <strong>
                {{ $profile?->hrFunction?->name
                    ?? 'Non renseignée' }}
            </strong>
        </div>

        <div class="adv-current-card">
            <span>Ministère</span>

            <strong>
                {{ $agent->ministry?->name
                    ?? 'Non affecté' }}
            </strong>
        </div>

    </div>


    <article class="adv-card">

        <div class="adv-card-header">
            <h2>Nouvelle situation de carrière</h2>
        </div>

        <div class="adv-body">

            @if ($errors->any())
                <div style="
                    margin-bottom:16px;
                    padding:12px;
                    background:#fff0f2;
                    border:1px solid #eeb8c1;
                    border-radius:7px;
                    color:#9d1632;
                ">
                    <ul style="margin:0;">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif


            <form
    method="POST"
    action="{{ route('responsable.agents.advancement.store', $agent) }}"
>
    @csrf

                <div class="adv-grid">

                    <div class="adv-field">
                        <label for="advancement_type">
                            Type d’opération *
                        </label>

                        <select
                            id="advancement_type"
                            name="advancement_type"
                            required
                        >
                            <option value="advancement">
                                Avancement
                            </option>

                            <option value="promotion">
                                Promotion
                            </option>

                            <option value="reclassification">
                                Reclassement
                            </option>
                        </select>
                    </div>


                    <div class="adv-field">
                        <label for="effective_at">
                            Date d’effet *
                        </label>

                        <input
                            id="effective_at"
                            type="date"
                            name="effective_at"
                            value="{{ old(
                                'effective_at',
                                now()->format('Y-m-d')
                            ) }}"
                            required
                        >
                    </div>


                    <div class="adv-field">
                        <label for="new_category_id">
                            Nouvelle catégorie *
                        </label>

                        <select
                            id="new_category_id"
                            name="new_category_id"
                            required
                        >
                            <option value="">
                                Choisir une catégorie
                            </option>

                            @foreach ($categories as $category)
                                <option
                                    value="{{ $category->id }}"
                                    @selected(
                                        old(
                                            'new_category_id',
                                            $profile?->category_id
                                        ) == $category->id
                                    )
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="adv-field">
                        <label for="new_grade_id">
                            Nouveau grade *
                        </label>

                        <select
                            id="new_grade_id"
                            name="new_grade_id"
                            required
                        >
                            <option value="">
                                Choisir un grade
                            </option>

                            @foreach ($grades as $grade)
                                <option
                                    value="{{ $grade->id }}"
                                    data-category-id="{{ $grade->category_id }}"
                                    @selected(
                                        old(
                                            'new_grade_id',
                                            $profile?->grade_id
                                        ) == $grade->id
                                    )
                                >
                                    {{ $grade->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="adv-field">
                        <label for="new_function_id">
                            Nouvelle fonction
                        </label>

                        <select
                            id="new_function_id"
                            name="new_function_id"
                        >
                            <option value="">
                                Conserver la fonction actuelle
                            </option>

                            @foreach ($functions as $function)
                                <option
                                    value="{{ $function->id }}"
                                >
                                    {{ $function->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>


                    <div class="adv-field">
                        <label for="reference">
                            Référence de l’acte *
                        </label>

                        <input
                            id="reference"
                            type="text"
                            name="reference"
                            value="{{ old('reference') }}"
                            placeholder="Ex. ARRÊTÉ-AVANCEMENT-2026-001"
                            required
                        >
                    </div>


                    <div class="adv-field full">
                        <label for="reason">
                            Motif / décision *
                        </label>

                        <textarea
                            id="reason"
                            name="reason"
                            required
                            placeholder="Indiquez la décision administrative justifiant cette évolution..."
                        >{{ old('reason') }}</textarea>
                    </div>

                </div>


                <div class="adv-actions">

                    <button
                        type="submit"
                        class="adv-btn adv-btn-primary"
                    >
                        Enregistrer l’opération
                    </button>

                    <a
                        href="{{ route(
                            'responsable.agents.show',
                            $agent
                        ) }}"
                        class="adv-btn adv-btn-yellow"
                    >
                        Annuler
                    </a>

                </div>

            </form>

        </div>

    </article>

</section>


<script>
document.addEventListener('DOMContentLoaded', function () {

    const category =
        document.getElementById('new_category_id');

    const grade =
        document.getElementById('new_grade_id');

    function filterGrades() {

        const categoryId = category.value;

        Array.from(grade.options).forEach(function (option) {

            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden =
                categoryId !== '' &&
                option.dataset.categoryId !== categoryId;
        });

        const selected =
            grade.options[grade.selectedIndex];

        if (
            selected &&
            selected.value &&
            categoryId &&
            selected.dataset.categoryId !== categoryId
        ) {
            grade.value = '';
        }
    }

    category.addEventListener(
        'change',
        filterGrades
    );

    filterGrades();
});
</script>

@endsection