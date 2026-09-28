<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'PNAE-RCA')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="public-layout">

<header class="main-header">
    <div class="brand-block">
        <a href="{{ route('home') }}" class="brand-link">
            <div class="rca-flag" aria-hidden="true">
                <span class="flag-star">★</span>
            </div>

            <div>
                <strong>PNAE-RCA</strong>
                <small>République Centrafricaine</small>
            </div>
        </a>
    </div>

    <nav class="main-nav">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ route('services') }}">Services</a>
        <a href="{{ route('home') }}#pnae">Le PNAE</a>
        <a href="{{ route('contact') }}">Contact</a>

        @auth
            @php
                $dashboardRoute = match(auth()->user()->role) {
                    'admin' => 'admin.dashboard',
                    'agent', 'responsable' => 'agent.dashboard',
                    default => 'citizen.dashboard',
                };
            @endphp

            <a href="{{ route($dashboardRoute) }}">
                Mon espace
            </a>

            <form
                method="POST"
                action="{{ route('logout') }}"
                class="logout-form"
            >
                @csrf

                <button type="submit" class="logout-button">
                    Déconnexion
                </button>
            </form>
        @else
            <a href="{{ route('login') }}">
                Connexion
            </a>

            <a
                href="{{ route('register') }}"
                class="account-btn"
            >
                Créer un compte
            </a>
        @endauth
    </nav>
</header>

<main>
    @yield('content')
</main>

<footer class="site-footer">
    <strong>République Centrafricaine</strong>

    <p>
        Plateforme Nationale d’Administration Électronique © {{ date('Y') }}
    </p>
</footer>

@stack('scripts')
</body>
</html>