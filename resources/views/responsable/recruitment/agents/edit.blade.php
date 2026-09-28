@extends('layouts.responsable')

@section('title', 'Modifier le dossier RH')

@section('content')

@php
    $profile = $agent->agentProfile;
@endphp

<style>
    .rh-edit-page {
        max-width: 1100px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .rh-edit-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        gap: 20px;
        margin-bottom: 20px;
    }

    .rh-edit-kicker {
        display: block;
        margin-bottom: 7px;
        color: #d6002f;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .rh-edit-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 30px;
    }

    .rh-edit-header p {
        margin: 7px 0 0;
        color: #65738a;
        font-size: 13px;
    }

    .rh-edit-card {
        background: #fff;
        border: 1px solid #d7e1ee;
        border-radius: 13px;
        overflow: hidden;
        box-shadow: 0 5px 18px rgba(20,54,98,.04);
        margin-bottom: 16px;
    }

    .rh-edit-card-header {
        padding: 16px 20px;
        border-bottom: 1px solid #e2e9f2;
    }

    .rh-edit-card-header h2 {
        margin: 0;
        color: #073b82;
        font-size: 17px;
    }

    .rh-edit-card-header p {
        margin: 4px 0 0;
        color: #7a879a;
        font-size: 11px;
    }

    .rh-edit-card-body {
        padding: 20px;
    }

    .rh-edit-grid {
        display: grid;
        grid-template-columns: repeat(2, minmax(0, 1fr));
        gap: 16px;
    }

    .rh-edit-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .rh-edit-field.full {
        grid-column: 1 / -1;
    }

    .rh-edit-field label {
        color: #17365f;
        font-size: 12px;
        font-weight: 800;
    }

    .rh-edit-field input,
    .rh-edit-field select,
    .rh-edit-field textarea {
        width: 100%;
        min-height: 40px;
        padding: 10px 12px;
        border: 1px solid #cbd8e9;
        border-radius: 7px;
        background: #fff;
        color: #183554;
        font-size: 13px;
        box-sizing: border-box;
    }

    .rh-edit-field textarea {
        min-height: 95px;
        resize: vertical;
    }

    .rh-edit-field input:focus,
    .rh-edit-field select:focus,
    .rh-edit-field textarea:focus {
        outline: none;
        border-color: #0756ad;
        box-shadow: 0 0 0 3px rgba(7,86,173,.08);
    }

    .rh-readonly {
        background: #eef3f9 !important;
        color: #68778b !important;
    }

    .rh-edit-actions {
        display: flex;
        justify-content: center;
        gap: 10px;
        margin-top: 22px;
    }

    .rh-edit-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-height: 40px;
        padding: 9px 18px;
        border: 0;
        border-radius: 8px;
        font-weight: 800;
        font-size: 13px;
        text-decoration: none;
        cursor: pointer;
    }

    .rh-edit-btn-primary {
        background: #073f91;
        color: #fff;
    }

    .rh-edit-btn-yellow {
        background: #ffc900;
        color: #092c60;
    }

    .rh-errors {
        margin-bottom: 16px;
        padding: 14px 18px;
        border: 1px solid #efb3bd;
        border-radius: 8px;
        background: #fff0f2;
        color: #9d1632;
        font-size: 12px;
    }

    @media (max-width: 760px) {
        .rh-edit-grid {
            grid-template-columns: 1fr;
        }

        .rh-edit-field.full {
            grid-column: auto;
        }

        .rh-edit-header {
            flex-direction: column;
        }
    }
</style>

<section class="rh-edit-page">

    <header class="rh-edit-header">

        <div>
            <span class="rh-edit-kicker">
                Gestion du dossier administratif
            </span>

            <h1>
                Modifier {{ $agent->name }}
            </h1>

            <p>
                Matricule :
                <strong>
                    {{ $profile?->matricule ?? 'Non attribué' }}
                </strong>
            </p>
        </div>

        <a
            href="{{ route(
                'responsable.agents.show',
                $agent
            ) }}"
            class="rh-edit-btn rh-edit-btn-yellow"
        >
            Retour au dossier RH
        </a>

    </header>


    @if ($errors->any())

        <div class="rh-errors">
            <strong>
                Certaines informations doivent être corrigées :
            </strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>

    @endif


    <form
        method="POST"
        action="{{ route(
            'responsable.recruitment.agents.update',
            $agent
        ) }}"
    >

        @csrf
        @method('PUT')


        <article class="rh-edit-card">

            <div class="rh-edit-card-header">
                <h2>Identité et coordonnées</h2>
                <p>
                    Informations personnelles et moyens de contact.
                </p>
            </div>

            <div class="rh-edit-card-body">

                <div class="rh-edit-grid">

                    <div class="rh-edit-field">
                        <label for="name">
                            Nom complet *
                        </label>

                        <input
                            id="name"
                            type="text"
                            name="name"
                            value="{{ old(
                                'name',
                                $agent->name
                            ) }}"
                            required
                        >
                    </div>


                    <div class="rh-edit-field">
                        <label for="email">
                            Adresse électronique *
                        </label>

                        <input
                            id="email"
                            type="email"
                            name="email"
                            value="{{ old(
                                'email',
                                $agent->email
                            ) }}"
                            required
                        >
                    </div>


                    <div class="rh-edit-field">
                        <label for="phone">
                            Téléphone
                        </label>

                        <input
                            id="phone"
                            type="text"
                            name="phone"
                            value="{{ old(
                                'phone',
                                $agent->phone
                            ) }}"
                        >
                    </div>


                    <div class="rh-edit-field">
                        <label>
                            Matricule national
                        </label>

                        <input
                            type="text"
                            value="{{ $profile?->matricule }}"
                            readonly
                            class="rh-readonly"
                        >
                    </div>


                    <div class="rh-edit-field">
                        <label for="birth_date">
                            Date de naissance
                        </label>

                        <input
                            id="birth_date"
                            type="date"
                            name="birth_date"
                            value="{{ old(
                                'birth_date',
                                $profile?->birth_date?->format(
                                    'Y-m-d'
                                )
                            ) }}"
                        >
                    </div>


                    <div class="rh-edit-field">
                        <label for="place_of_birth">
                            Lieu de naissance
                        </label>

                        <input
                            id="place_of_birth"
                            type="text"
                            name="place_of_birth"
                            value="{{ old(
                                'place_of_birth',
                                $profile?->place_of_birth
                            ) }}"
                        >
                    </div>

                </div>

            </div>

        </article>


        <article class="rh-edit-card">

            <div class="rh-edit-card-header">
                <h2>Situation professionnelle</h2>
                <p>
                    Affectation fonctionnelle, grade et catégorie.
                </p>
            </div>

            <div class="rh-edit-card-body">

                <div class="rh-edit-grid">

                    <div class="rh-edit-field">
                        <label>
                            Ministère actuel
                        </label>

                        <input
                            type="text"
                            value="{{ $profile?->ministry?->name
                                ?? $agent->ministry?->name }}"
                            readonly
                            class="rh-readonly"
                        >
                    </div>

<div class="rh-edit-field">
    <label for="service_id">Service</label>

    <select id="service_id" name="service_id">
        <option value="">Aucun service</option>

        @foreach ($services as $service)
            <option
                value="{{ $service->id }}"
                @selected(
                    old(
                        'service_id',
                        $profile?->service_id
                    ) == $service->id
                )
            >
                {{ $service->name }}
            </option>
        @endforeach
    </select>
</div>


                    <div class="rh-edit-field">
    <label for="function_id">Fonction</label>

    <select id="function_id" name="function_id">
        <option value="">Non renseignée</option>

        @foreach ($functions as $function)
            <option
                value="{{ $function->id }}"
                @selected(
                    old(
                        'function_id',
                        $profile?->function_id
                    ) == $function->id
                )
            >
                {{ $function->name }}
            </option>
        @endforeach
    </select>
</div>


                    <div class="rh-edit-field">
    <label for="grade_id">Grade</label>

    <select id="grade_id" name="grade_id">
        <option value="">Non renseigné</option>

        @foreach ($grades as $grade)
            <option
                value="{{ $grade->id }}"
                data-category-id="{{ $grade->category_id }}"
                @selected(
                    old(
                        'grade_id',
                        $profile?->grade_id
                    ) == $grade->id
                )
            >
                {{ $grade->name }}
            </option>
        @endforeach
    </select>
</div>


                    <div class="rh-edit-field">
    <label for="category_id">Catégorie</label>

    <select id="category_id" name="category_id">
        <option value="">Non renseignée</option>

        @foreach ($categories as $category)
            <option
                value="{{ $category->id }}"
                @selected(
                    old(
                        'category_id',
                        $profile?->category_id
                    ) == $category->id
                )
            >
                {{ $category->name }}
            </option>
        @endforeach
    </select>
</div>
                </div>

            </div>

        </article>


        <article class="rh-edit-card">

            <div class="rh-edit-card-header">
                <h2>Carrière et références</h2>
                <p>
                    Dates principales du parcours administratif.
                </p>
            </div>

            <div class="rh-edit-card-body">

                <div class="rh-edit-grid">

                    <div class="rh-edit-field">
                        <label for="recruitment_date">
                            Date de recrutement
                        </label>

                        <input
                            id="recruitment_date"
                            type="date"
                            name="recruitment_date"
                            value="{{ old(
                                'recruitment_date',
                                $profile?->recruitment_date
                                    ?->format('Y-m-d')
                            ) }}"
                        >
                    </div>


                    <div class="rh-edit-field">
                        <label for="appointment_date">
                            Date de prise de fonction
                        </label>

                        <input
                            id="appointment_date"
                            type="date"
                            name="appointment_date"
                            value="{{ old(
                                'appointment_date',
                                $profile?->appointment_date
                                    ?->format('Y-m-d')
                            ) }}"
                        >
                    </div>


                    <div class="rh-edit-field full">
                        <label for="notes">
                            Observations RH
                        </label>

                        <textarea
                            id="notes"
                            name="notes"
                        >{{ old(
                            'notes',
                            $profile?->notes
                        ) }}</textarea>
                    </div>


                    <div class="rh-edit-field full">
                        <label for="update_reason">
                            Motif de cette modification *
                        </label>

                        <textarea
                            id="update_reason"
                            name="update_reason"
                            required
                            placeholder="Ex. Mise à jour du grade après décision administrative, correction des informations personnelles..."
                        >{{ old('update_reason') }}</textarea>
                    </div>

                </div>

            </div>

        </article>


        <div class="rh-edit-actions">

            <button
                type="submit"
                class="rh-edit-btn rh-edit-btn-primary"
            >
                Enregistrer les modifications
            </button>

            <a
                href="{{ route(
                    'responsable.agents.show',
                    $agent
                ) }}"
                class="rh-edit-btn rh-edit-btn-yellow"
            >
                Annuler
            </a>

        </div>

    </form>

</section>

@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    const category = document.getElementById('category_id');
    const grade = document.getElementById('grade_id');

    if (!category || !grade) {
        return;
    }

    function filterGrades() {
        const selectedCategory = category.value;

        Array.from(grade.options).forEach(function (option) {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden =
                selectedCategory !== '' &&
                option.dataset.categoryId !== selectedCategory;
        });

        const selectedGrade = grade.options[grade.selectedIndex];

        if (
            selectedGrade &&
            selectedGrade.value &&
            selectedCategory &&
            selectedGrade.dataset.categoryId !== selectedCategory
        ) {
            grade.value = '';
        }
    }

    category.addEventListener('change', filterGrades);

    filterGrades();
});
</script>