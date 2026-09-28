@extends('layouts.app')

@section('content')

<section class="hero-rca">
    <div class="hero-red"></div>
    <div class="hero-star">★</div>

    <div class="container text-center hero-content">
        <div class="hero-badge">
            République Centrafricaine · Services publics numériques
        </div>

        <h1>
            Plateforme Nationale<br>
            d'Administration Électronique
        </h1>

        <p>
            Effectuez vos démarches administratives en ligne,<br>
            suivez vos dossiers et recevez vos documents officiels<br>
            depuis un espace sécurisé.
        </p>

        <div class="mt-4">
            <a href="{{ route('services') }}" class="btn btn-primary-rca me-2">
                Commencer une démarche
            </a>
            <a
              href="{{ route('public.tracking.form') }}"
                class="btn btn-light"
            >
                   Suivre une demande
           </a>
        </div>
    </div>
</section>

<div class="container my-4">
    <div class="alert alert-primary d-flex justify-content-between align-items-center announcement">
        <div>
            📢 <strong>Annonce importante</strong>
            <span class="mx-2">|</span>
            La digitalisation des services publics, un engagement pour la transparence et la proximité.
        </div>
        <a
          href="{{ route('public.announcements.index') }}"
          class="announcements-link"
      >
           Voir toutes les annonces →
       </a>
    </div>
</div>

<section class="services-section">

    <h2>Services les plus demandés</h2>

    <p>
        Accédez rapidement aux services publics disponibles
    </p>

    <div class="row">
        @foreach($ministries as $ministry)
            <div>
                <div class="service-card">

                    <div class="icon">🏛️</div>

                    <h3>{{ $ministry->name }}</h3>

                    <p>
                        {{ $ministry->procedures_count }}
                        démarche(s) disponible(s).
                    </p>

                    <a href="{{ route('services.ministry', $ministry) }}">
                        Accéder au service →
                    </a>

                </div>
            </div>
        @endforeach
    </div>

</section>
 

<section class="container my-4">
    <div class="row g-0 features-bar text-white">
        <div class="col-md-3 feature-item">🛡️ <strong>Sécurisé</strong><br><small>Vos données sont protégées</small></div>
        <div class="col-md-3 feature-item">🕒 <strong>Disponible 24/7</strong><br><small>Accédez à nos services à tout moment</small></div>
        <div class="col-md-3 feature-item">✅ <strong>Fiable</strong><br><small>Des services officiels et certifiés</small></div>
        <div class="col-md-3 feature-item">👥 <strong>Proche de vous</strong><br><small>Un service public plus accessible</small></div>
    </div>
</section>

@endsection