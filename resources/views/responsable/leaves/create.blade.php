@extends('layouts.responsable')

@section('title', 'Congé / Absence')

@section('content')

<style>
    .leave-page {
        max-width: 1050px;
        margin: 0 auto;
        padding: 28px 18px 40px;
    }

    .leave-header {
        display: flex;
        justify-content: space-between;
        gap: 20px;
        margin-bottom: 20px;
    }

    .leave-kicker {
        display: block;
        margin-bottom: 7px;
        color: #d6002f;
        font-size: 11px;
        font-weight: 800;
        letter-spacing: .1em;
        text-transform: uppercase;
    }

    .leave-header h1 {
        margin: 0;
        color: #073b82;
        font-size: 29px;
    }

    .leave-card {
        background: #fff;
        border: 1px solid #d7e1ee;
        border-radius: 13px;
        padding: 22px;
    }

    .leave-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 16px;
    }

    .leave-field {
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .leave-field.full {
        grid-column: 1 / -1;
    }

    .leave-field label {
        font-size: 12px;
        font-weight: 800;
        color: #17365f;
    }

    .leave-field input,
    .leave-field select,
    .leave-field textarea {
        padding: 10px 12px;
        border: 1px solid #cbd8e8;
        border-radius: 7px;
    }

    .leave-field textarea {
        min-height: 100px;
    }

    .leave-actions {
        margin-top: 22px;
        display: flex;
        justify-content: center;
        gap: 10px;
    }

    .leave-btn {
        border: 0;
        border-radius: 8px;
        padding: 10px 18px;
        font-weight: 800;
        text-decoration: none;
        cursor: pointer;
    }

    .leave-btn-blue {
        background: #073f91;
        color: #fff;
    }

    .leave-btn-yellow {
        background: #ffc900;
        color: #092d61;
    }
</style>

<section class="leave-page">

    <header class="leave-header">

        <div>
            <span class="leave-kicker">
                Gestion des congés et absences
            </span>

            <h1>
                Nouveau congé / absence
            </h1>

            <p>
                {{ $agent->name }}
            </p>
        </div>

        <a
            href="{{ route(
                'responsable.agents.leaves.index',
                $agent
            ) }}"
            class="leave-btn leave-btn-yellow"
        >
            Retour
        </a>

    </header>


    <form
        method="POST"
        action="{{ route(
            'responsable.agents.leaves.store',
            $agent
        ) }}"
    >

        @csrf

        <article class="leave-card">

            <div class="leave-grid">

                <div class="leave-field">
                    <label>Type *</label>

                    <select
                        name="leave_type"
                        required
                    >
                        <option value="">
                            Choisir
                        </option>

                        <option value="annual_leave">
                            Congé annuel
                        </option>

                        <option value="sick_leave">
                            Congé maladie
                        </option>

                        <option value="maternity_leave">
                            Congé maternité
                        </option>

                        <option value="paternity_leave">
                            Congé paternité
                        </option>

                        <option value="administrative_leave">
                            Congé administratif
                        </option>

                        <option value="training">
                            Formation
                        </option>

                        <option value="authorized_absence">
                            Absence autorisée
                        </option>

                        <option value="unjustified_absence">
                            Absence injustifiée
                        </option>

                        <option value="other">
                            Autre
                        </option>
                    </select>
                </div>


                <div class="leave-field">
                    <label>Référence administrative</label>

                    <input
                        type="text"
                        name="reference"
                        value="{{ old('reference') }}"
                        placeholder="Ex. DECISION-CONGE-2026-001"
                    >
                </div>


                <div class="leave-field">
                    <label>Date de début *</label>

                    <input
                        type="date"
                        name="start_date"
                        value="{{ old('start_date') }}"
                        required
                    >
                </div>


                <div class="leave-field">
                    <label>Date de fin *</label>

                    <input
                        type="date"
                        name="end_date"
                        value="{{ old('end_date') }}"
                        required
                    >
                </div>


                <div class="leave-field full">
                    <label>Motif *</label>

                    <textarea
                        name="reason"
                        required
                    >{{ old('reason') }}</textarea>
                </div>

            </div>


            <div class="leave-actions">

                <button
                    class="leave-btn leave-btn-blue"
                    type="submit"
                >
                    Enregistrer
                </button>

                <a
                    href="{{ route(
                        'responsable.agents.show',
                        $agent
                    ) }}"
                    class="leave-btn leave-btn-yellow"
                >
                    Annuler
                </a>

            </div>

        </article>

    </form>

</section>

@endsection