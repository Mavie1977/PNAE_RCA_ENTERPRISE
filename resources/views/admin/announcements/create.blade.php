@extends('layouts.admin')

@section('title', 'Nouvelle annonce')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Créer une annonce nationale</h1>
        <p>Ajoutez une information visible sur la page d’accueil.</p>
    </div>

    <div class="form-card">

        <form method="POST" action="{{ route('admin.announcements.store') }}">
            @csrf

            <div class="form-grid">

                <div class="form-group">
                    <label>Titre de l’annonce</label>
                    <input type="text" name="title" value="{{ old('title') }}" required>
                </div>

                <div class="form-group">
                    <label>Type</label>
                    <select name="type" required>
                        <option value="info">Information</option>
                        <option value="urgent">Urgent</option>
                        <option value="maintenance">Maintenance</option>
                        <option value="communique">Communiqué</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Date de début</label>
                    <input type="date" name="start_date" value="{{ old('start_date') }}">
                </div>

                <div class="form-group">
                    <label>Date de fin</label>
                    <input type="date" name="end_date" value="{{ old('end_date') }}">
                </div>

            </div>

            <div class="form-group mt-4">
                <label>Contenu</label>
                <textarea name="content" rows="6">{{ old('content') }}</textarea>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">
                    Publier l’annonce
                </button>

                <a href="{{ route('admin.announcements.index') }}" class="btn-rca-secondary">
                    Retour
                </a>
            </div>

        </form>

    </div>

</section>
@endsection