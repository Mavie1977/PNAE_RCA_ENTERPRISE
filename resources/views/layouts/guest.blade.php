<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        @yield('title', 'Authentification — PNAE-RCA')
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="guest-layout">

<header class="guest-header">
    <a href="{{ route('home') }}" class="brand-link">
        <div class="rca-flag" aria-hidden="true">
            <span class="flag-star">★</span>
        </div>

        <div>
            <strong>PNAE-RCA V10</strong>
            <small>République Centrafricaine</small>
        </div>
    </a>

    <a href="{{ route('home') }}" class="btn-rca-secondary">
        Retour au portail
    </a>
</header>

<main class="guest-main">
    @yield('content')
</main>

<footer class="site-footer">
    <strong>République Centrafricaine</strong>

    <p>
        Plateforme Nationale d’Administration Électronique © {{ date('Y') }}
    </p>
</footer>

</body>
</html>