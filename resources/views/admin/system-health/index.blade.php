@extends('layouts.admin')

@section('title', 'Santé du système')

@section('content')

<section class="page-section">

    <div class="page-heading page-heading-actions">

        <div>
            <span class="page-kicker">
                SUPERVISION TECHNIQUE
            </span>

            <h1>Santé de la plateforme</h1>

            <p>
                Vérification des principaux composants nécessaires
                au fonctionnement de PNAE-RCA.
            </p>
        </div>

        <div class="system-global-status {{
            $globalOperational
                ? 'system-status-ok'
                : 'system-status-warning'
        }}">
            <span class="system-status-dot"></span>

            <div>
                <small>État global</small>

                <strong>
                    {{ $globalOperational
                        ? 'Opérationnel'
                        : 'Attention requise' }}
                </strong>
            </div>
        </div>

    </div>

    @if (
        app()->environment('production')
        && config('app.debug')
    )
        <div class="alert alert-danger">
            <strong>Alerte de sécurité :</strong>
            APP_DEBUG est activé en production.
        </div>
    @endif

    <div class="system-health-grid">

        @foreach ($health as $component)

            <article class="system-health-card {{
                $component['operational']
                    ? 'system-health-success'
                    : 'system-health-danger'
            }}">

                <div class="system-health-indicator"></div>

                <div class="system-health-content">

                    <span>
                        {{ $component['label'] }}
                    </span>

                    <strong>
                        {{ $component['value'] }}
                    </strong>

                </div>

                <div class="system-health-icon">
                    {{ $component['operational'] ? '✓' : '!' }}
                </div>

            </article>

        @endforeach

    </div>

    <div class="enterprise-card system-technical-card">

        <div class="enterprise-card-header">

            <div>
                <h2>Informations techniques</h2>

                <p>
                    Configuration active de l’environnement Laravel.
                </p>
            </div>

            <span class="enterprise-card-icon">
                ⚙️
            </span>

        </div>

        <div class="technical-information-grid">

            <div>
                <span>Version PHP</span>
                <strong>{{ $technical['php_version'] }}</strong>
            </div>

            <div>
                <span>Version Laravel</span>
                <strong>{{ $technical['laravel_version'] }}</strong>
            </div>

            <div>
                <span>Base de données</span>
                <strong>{{ $technical['database_driver'] }}</strong>
            </div>

            <div>
                <span>Cache</span>
                <strong>{{ $technical['cache_driver'] }}</strong>
            </div>

            <div>
                <span>Sessions</span>
                <strong>{{ $technical['session_driver'] }}</strong>
            </div>

            <div>
                <span>File d’attente</span>
                <strong>{{ $technical['queue_driver'] }}</strong>
            </div>

            <div>
                <span>Stockage</span>
                <strong>{{ $technical['filesystem_disk'] }}</strong>
            </div>

            <div>
                <span>Messagerie</span>
                <strong>{{ $technical['mail_driver'] }}</strong>
            </div>

            <div>
                <span>URL de l’application</span>
                <strong>{{ $technical['app_url'] }}</strong>
            </div>

            <div>
                <span>Fuseau horaire</span>
                <strong>{{ $technical['timezone'] }}</strong>
            </div>

        </div>

    </div>

    <div class="enterprise-card system-security-card">

        <div class="enterprise-card-header">

            <div>
                <h2>Recommandations de production</h2>

                <p>
                    Points de contrôle avant une mise en ligne publique.
                </p>
            </div>

            <span class="enterprise-card-icon">
                🛡️
            </span>

        </div>

        <div class="system-recommendations">

            <div>
                <span class="recommendation-status {{
                    config('app.debug')
                        ? 'recommendation-danger'
                        : 'recommendation-success'
                }}"></span>

                <p>
                    <strong>Mode debug :</strong>
                    {{ config('app.debug')
                        ? 'à désactiver avant la production.'
                        : 'configuration sécurisée.' }}
                </p>
            </div>

            <div>
                <span class="recommendation-status {{
                    app()->environment('production')
                        ? 'recommendation-success'
                        : 'recommendation-warning'
                }}"></span>

                <p>
                    <strong>Environnement :</strong>
                    {{ app()->environment('production')
                        ? 'production active.'
                        : 'environnement local ou de test.' }}
                </p>
            </div>

            <div>
                <span class="recommendation-status {{
                    str_starts_with(
                        config('app.url'),
                        'https://'
                    )
                        ? 'recommendation-success'
                        : 'recommendation-warning'
                }}"></span>

                <p>
                    <strong>HTTPS :</strong>
                    {{ str_starts_with(
                        config('app.url'),
                        'https://'
                    )
                        ? 'URL sécurisée.'
                        : 'HTTPS requis lors du déploiement.' }}
                </p>
            </div>

            <div>
                <span class="recommendation-status {{
                    config('mail.default') === 'log'
                        ? 'recommendation-warning'
                        : 'recommendation-success'
                }}"></span>

                <p>
                    <strong>Messagerie :</strong>
                    {{ config('mail.default') === 'log'
                        ? 'les courriels sont encore écrits dans les logs.'
                        : 'transport de messagerie configuré.' }}
                </p>
            </div>

        </div>

    </div>

</section>

@endsection