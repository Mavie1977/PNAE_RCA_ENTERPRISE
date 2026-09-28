@extends('layouts.guest')

@section('content')

<section class="page-section">
    <div class="form-card">

        <div class="form-header">
            <h1>Créer un compte citoyen</h1>
            <p>Inscrivez-vous pour déposer et suivre vos démarches administratives.</p>
        </div>

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Nom complet</label>
                    <input type="text" name="name" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Téléphone</label>
                    <input type="text" name="phone">
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
                <button type="submit" class="btn-rca-primary">
                    Créer mon compte
                </button>

                <a href="{{ route('login') }}" class="btn-rca-secondary">
                    Déjà un compte ? Connexion
                </a>
            </div>
        </form>

    </div>
</section>

@endsection