@extends('layouts.admin')

@section('title', 'Créer un agent')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Créer un agent public</h1>
        <p>Ajoutez un agent habilité à traiter les demandes administratives.</p>
    </div>

    <div class="form-card">

        <form method="POST" action="{{ route('admin.agents.store') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="name" value="{{ old('name') }}" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="phone" value="{{ old('phone') }}">
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                </div>

                <div class="form-group">
                    <label>Confirmer le mot de passe</label>
                    <input type="password" name="password_confirmation" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">Créer l’agent</button>
                <a href="{{ route('admin.agents.index') }}" class="btn-rca-secondary">Retour</a>
            </div>
        </form>

    </div>

</section>
@endsection