<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'PNAE-RCA_Application')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>

<header class="topbar">
    <a href="{{ route('home') }}" class="brand">
        <div class="flag-mini"></div>
        <div>
            <strong>PNAE-RCA</strong>
            <span>République Centrafricaine</span>
        </div>
    </a>

    <nav class="main-nav">
        <a href="{{ route('home') }}">Accueil</a>
        <a href="{{ route('services') }}">Services</a>
        <a href="#">Le PNAE</a>
        <a href="{{ route('contact') }}">Contact</a>

       @auth
    @php
        $user = auth()->user();

        $dashboardRoute = match ($user->role) {
            'admin' => 'admin.dashboard',
            'agent', 'responsable' => 'agent.dashboard',
            'citoyen' => 'citizen.dashboard',
            default => 'dashboard',
        };

        $roleLabel = match ($user->role) {
            'admin' => 'Administrateur',
            'agent' => 'Agent public',
            'responsable' => 'Responsable',
            'citoyen' => 'Citoyen',
            default => ucfirst($user->role),
        };
    @endphp

    <a href="{{ route($dashboardRoute) }}" class="nav-space-link">
        Mon espace
    </a>

    <div class="connected-user-box">
        <span class="connected-user-role">
            {{ $roleLabel }}
        </span>

        <strong>{{ $user->name }}</strong>

        <small>{{ $user->email }}</small>
    </div>

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
    <a href="{{ route('login') }}">Connexion</a>

    <a class="account-btn" href="{{ route('register') }}">
        Créer un compte
    </a>
@endauth
    </nav>
</header>

@if(session('success'))
    <div class="container mt-3">
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    </div>
@endif

@if($errors->any())
    <div class="container mt-3">
        <div class="alert alert-danger">
            <strong>Erreur :</strong> veuillez vérifier les informations saisies.
        </div>
    </div>
@endif

<main class="page-main">
    @yield('content')
</main>

<footer class="footer-rca">
    <div class="container text-center">
        <h5>République Centrafricaine</h5>
        <p>Plateforme Nationale d’Administration Électronique © {{ date('Y') }}</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>