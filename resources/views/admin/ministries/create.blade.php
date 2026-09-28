@extends('layouts.admin')

@section('title', 'Nouveau ministère')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Créer un ministère</h1>
        <p>Ajoutez un ministère à la Plateforme Nationale d’Administration Électronique.</p>
    </div>

    <div class="form-card">

        <form method="POST" action="{{ route('admin.ministries.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Nom du ministère</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>Description</label>
                    <textarea name="description" rows="5">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">
                    Enregistrer
                </button>

                <a href="{{ route('admin.ministries.index') }}" class="btn-rca-secondary">
                    Retour
                </a>
            </div>

        </form>

    </div>

</section>
@endsection