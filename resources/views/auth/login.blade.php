@extends('layouts.guest')

@section('content')

<section class="page-section">
    <div class="form-card">

        <div class="form-header">
            <h1>Connexion</h1>
            <p>Accédez à votre espace sécurisé PNAE-RCA.</p>
        </div>

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" required>
                </div>

                <div class="form-group">
                    <label>Mot de passe</label>
                    <input type="password" name="password" required>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">
                    Se connecter
                </button>

                <a href="{{ route('register') }}" class="btn-rca-secondary">
                    Créer un compte citoyen
                </a>
            </div>
        </form>

    </div>
</section>

@endsection