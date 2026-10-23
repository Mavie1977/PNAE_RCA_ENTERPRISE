
@extends('layouts.guest')

@section('content')

<section class="page-section">
    <div class="form-card">

        <div class="form-header">
            <h1>Connexion</h1>
            <p>Accédez à votre espace sécurisé PNAE-RCA.</p>
        </div>

        {{-- Affichage des erreurs de connexion --}}
        @if ($errors->any())
            <div role="alert"
                 style="background:#fff1f0;color:#a61b1b;border:1px solid #f1b5b5;padding:15px;border-radius:8px;margin-bottom:20px;">
                <strong>Connexion impossible</strong>
                <ul style="margin:8px 0 0 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('warning'))
            <div role="alert"
                 style="background:#fff8df;color:#775000;padding:15px;border-radius:8px;margin-bottom:20px;">
                {{ session('warning') }}
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div class="form-grid">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input
                        id="email"
                        type="email"
                        name="email"
                        value="{{ old('email') }}"
                        autocomplete="username"
                        required
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input
                        id="password"
                        type="password"
                        name="password"
                        autocomplete="current-password"
                        required
                    >
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
