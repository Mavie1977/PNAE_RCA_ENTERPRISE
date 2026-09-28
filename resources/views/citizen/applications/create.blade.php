@extends('layouts.citizen')

@section('title', 'Nouvelle demande administrative')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Nouvelle demande administrative</h1>
        <p>Sélectionnez une démarche, ajoutez vos documents et déposez votre dossier.</p>
    </div>

    <div class="form-card">
        <form method="POST" action="{{ route('citizen.application.store') }}" enctype="multipart/form-data">
            @csrf

            <div class="form-header">
                <h2>1. Choisir une démarche</h2>
                <p>Cochez le service administratif concerné.</p>
            </div>

            <div class="procedure-grid">
                @foreach($procedures as $procedure)
                    <label class="procedure-option">
                        <input type="radio" name="procedure_id" value="{{ $procedure->id }}" required>

                        <div class="procedure-box">
                            <strong>{{ $procedure->title }}</strong>
                            <span>{{ $procedure->ministry->name ?? 'Ministère concerné' }}</span>
                            <small>{{ number_format($procedure->fee, 0, ',', ' ') }} FCFA</small>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="form-header mt-5">
                <h2>2. Informations complémentaires</h2>
            </div>

            <div class="form-grid">
                <div class="form-group">
                    <label>Priorité</label>
                    <select name="priority">
                        <option value="normale">Normale</option>
                        <option value="urgente">Urgente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Documents justificatifs</label>
                    <input type="file" name="documents[]" multiple accept=".pdf,.jpg,.jpeg,.png">
                    <small class="form-help">PDF, JPG, PNG — 5 Mo maximum par fichier.</small>
                </div>
            </div>

            <div class="form-group mt-4">
                <label>Message complémentaire</label>
                <textarea name="message" rows="5" placeholder="Expliquez votre demande..."></textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">Déposer la demande</button>
                <a href="{{ route('citizen.dashboard') }}" class="btn-rca-secondary">Retour</a>
            </div>
        </form>
    </div>

</section>
@endsection