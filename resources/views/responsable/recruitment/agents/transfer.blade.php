@extends('layouts.responsable')

@section('title', 'Mutation d’un agent')

@section('content')

<section class="decision-dashboard">

    <div class="decision-heading">
        <div>
            <span class="decision-kicker">
                MOBILITÉ ADMINISTRATIVE
            </span>

            <h1>Mutation d’un agent</h1>

            <p>
                Changez le ministère d’affectation de
                {{ $agent->name }}.
            </p>
        </div>

        <a
            href="{{ route('responsable.agents.index') }}"
            class="btn-rca-secondary"
        >
            Retour à la liste
        </a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>La mutation ne peut pas être enregistrée.</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <article class="decision-panel">

        <div class="decision-panel-header">
            <div>
                <h2>{{ $agent->name }}</h2>

                <p>
                    Affectation actuelle :
                    <strong>
                        {{ $agent->ministry?->name ?? 'Non affecté' }}
                    </strong>
                </p>
            </div>

            <span>🔄</span>
        </div>

        <form
            method="POST"
            action="{{ route(
                'responsable.recruitment.agents.transfer',
                $agent
            ) }}"
            class="enterprise-form"
        >
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label for="ministry_id">
                        Nouveau ministère
                    </label>

                    <select
                        id="ministry_id"
                        name="ministry_id"
                        required
                    >
                        <option value="">
                            Choisir le ministère d’accueil
                        </option>

                        @foreach ($ministries as $ministry)
                            <option
                                value="{{ $ministry->id }}"
                                @selected(
                                    old('ministry_id')
                                    == $ministry->id
                                )
                            >
                                {{ $ministry->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="effective_at">
                        Date d’effet
                    </label>

                    <input
                        id="effective_at"
                        type="date"
                        name="effective_at"
                        value="{{ old(
                            'effective_at',
                            now()->format('Y-m-d')
                        ) }}"
                    >
                </div>

                <div class="form-group">
                    <label for="reference">
                        Référence de l’acte
                    </label>

                    <input
                        id="reference"
                        type="text"
                        name="reference"
                        value="{{ old('reference') }}"
                        placeholder="Ex. DÉCISION-MUT-2026-014"
                    >
                </div>

                <div class="form-group form-group-full">
                    <label for="reason">
                        Motif de la mutation
                    </label>

                    <textarea
                        id="reason"
                        name="reason"
                        rows="5"
                        required
                    >{{ old('reason') }}</textarea>
                </div>

            </div>

            <div class="form-actions">
                <button
                    type="submit"
                    class="btn-rca-primary"
                >
                    Enregistrer la mutation
                </button>

                <a
                    href="{{ route(
                        'responsable.agents.index'
                    ) }}"
                    class="btn-rca-secondary"
                >
                    Annuler
                </a>
            </div>

        </form>

    </article>

</section>

@endsection