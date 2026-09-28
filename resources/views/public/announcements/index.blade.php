@extends('layouts.app')

@section('title', 'Annonces publiques')

@section('content')

<section class="public-enterprise-page">

    <div class="public-enterprise-heading">

        <span class="public-kicker">
            INFORMATION PUBLIQUE
        </span>

        <h1>Toutes les annonces</h1>

        <p>
            Consultez les informations officielles, nouveautés et communications
            publiées par l’administration nationale.
        </p>

    </div>

    <div class="announcements-hero-card">

        <div class="announcements-hero-icon">
            📢
        </div>

        <div>
            <h2>Actualités administratives</h2>

            <p>
                Retrouvez ici les principales informations relatives
                aux services publics numériques de la PNAE-RCA.
            </p>
        </div>

    </div>

    <div class="announcements-toolbar">

        <div>
            <strong>
                {{ $announcements->total() }}
            </strong>

            <span>
                annonce(s) publiée(s)
            </span>
        </div>

        <a
            href="{{ route('home') }}"
            class="public-btn public-btn-secondary"
        >
            Retour à l’accueil
        </a>

    </div>

    <div class="announcements-grid">

        @forelse ($announcements as $announcement)

            <article class="announcement-card">

                <div class="announcement-card-top">

                    <span class="announcement-category">
                        COMMUNIQUÉ OFFICIEL
                    </span>

                    <span class="announcement-date">
                        {{ $announcement->created_at?->format('d/m/Y') }}
                    </span>

                </div>

                <div class="announcement-card-icon">
                    📣
                </div>

                <h2>
                    {{ $announcement->title }}
                </h2>

                <p class="announcement-excerpt">
                    {{
                        \Illuminate\Support\Str::limit(
                            $announcement->content
                                ?? $announcement->description
                                ?? '',
                            240
                        )
                    }}
                </p>

                <div class="announcement-card-footer">

                    <span>
                        PNAE-RCA
                    </span>

                    @if(
                        \Illuminate\Support\Facades\Route::has(
                            'public.announcements.show'
                        )
                    )
                        <a
                            href="{{ route(
                                'public.announcements.show',
                                $announcement
                            ) }}"
                            class="announcement-read-link"
                        >
                            Lire la suite →
                        </a>
                    @endif

                </div>

            </article>

        @empty

            <div class="public-empty-state">

                <div class="public-empty-icon">
                    📭
                </div>

                <h2>Aucune annonce disponible</h2>

                <p>
                    Aucune communication publique n’est disponible actuellement.
                </p>

                <a
                    href="{{ route('home') }}"
                    class="public-btn public-btn-primary"
                >
                    Retour à l’accueil
                </a>

            </div>

        @endforelse

    </div>

    @if ($announcements->hasPages())
        <div class="public-pagination">
            {{ $announcements->links() }}
        </div>
    @endif

    <div class="announcements-bottom-banner">

        <div>
            <span>🇨🇫</span>

            <div>
                <strong>
                    Plateforme Nationale d’Administration Électronique
                </strong>

                <p>
                    Une administration plus proche, plus rapide et plus transparente.
                </p>
            </div>
        </div>

        <a
            href="{{ route('home') }}"
            class="public-btn public-btn-primary"
        >
            Accéder au portail public
        </a>

    </div>

</section>

@endsection