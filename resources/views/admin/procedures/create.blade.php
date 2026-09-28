@extends('layouts.admin')

@section('title', 'Nouvelle démarche')

@section('content')

<section class="page-section">

    <div class="page-heading">
        <h1>Créer une démarche</h1>
        <p>Ajoutez un service public numérique à la plateforme PNAE-RCA.</p>
    </div>

    <div class="form-card">

        <form method="POST" action="{{ route('admin.procedures.store') }}">
            @csrf

            <div class="form-header">
                <h2>Informations générales</h2>
                <p>Renseignez les informations principales de la démarche.</p>
            </div>

            <div class="form-grid">

                <div class="form-group">
                    <label>Ministère</label>
                    <select name="ministry_id" required>
                        <option value="">Choisir un ministère</option>

                        @foreach($ministries as $ministry)
                            <option value="{{ $ministry->id }}" @selected(old('ministry_id') == $ministry->id)>
                                {{ $ministry->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label>Nom de la démarche</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ old('title') }}"
                        placeholder="Ex : Passeport, Quitus fiscal..."
                        required>
                </div>

                <div class="form-group">
                    <label>Délai officiel en jours</label>
                    <input
                        type="number"
                        name="processing_days"
                        value="{{ old('processing_days', 7) }}"
                        min="1"
                        required>
                </div>

                <div class="form-group">
                    <label>Frais administratifs</label>
                    <input
                        type="number"
                        name="fee"
                        value="{{ old('fee', 0) }}"
                        min="0"
                        step="100">
                </div>

            </div>

            <div class="form-group mt-4">
                <label>Description</label>
                <textarea
                    name="description"
                    rows="5"
                    placeholder="Décrivez brièvement cette démarche administrative...">{{ old('description') }}</textarea>
            </div>

            <div class="form-group mt-4">
                <label>Documents obligatoires</label>
                <textarea
                    name="required_documents"
                    rows="5"
                    placeholder="Ex : Acte de naissance, photo d'identité, certificat de résidence...">{{ old('required_documents') }}</textarea>

                <small class="form-help">
                    Saisissez les pièces demandées, séparées par des virgules ou une par ligne.
                </small>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">
                    Enregistrer la démarche
                </button>

                <a href="{{ route('admin.procedures.index') }}" class="btn-rca-secondary">
                    Retour
                </a>
            </div>

        </form>

    </div>

</section>

@endsection