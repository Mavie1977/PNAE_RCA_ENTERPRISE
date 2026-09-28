@extends(
    auth()->user()->role === 'admin'
        ? 'layouts.admin'
        : (
            in_array(
                auth()->user()->role,
                ['agent', 'responsable'],
                true
            )
                ? 'layouts.agent'
                : 'layouts.citizen'
        )
)

@section('title', 'Mes notifications')

@section('content')

<section class="page-section">

    <div class="page-heading page-heading-actions">

        <div>
            <span class="page-kicker">
                CENTRE D’INFORMATION
            </span>

            <h1>Mes notifications</h1>

            <p>
                Consultez les événements importants liés à votre compte
                et à vos dossiers.
            </p>
        </div>

        @if (auth()->user()->unreadNotifications()->exists())
            <form
                method="POST"
                action="{{ route('notifications.read-all') }}"
            >
                @csrf
                @method('PATCH')

                <button
                    type="submit"
                    class="btn-rca-primary"
                >
                    Tout marquer comme lu
                </button>
            </form>
        @endif

    </div>

    <div class="enterprise-card">

        @forelse ($notifications as $notification)

            @php
                $data = $notification->data;
                $isUnread = $notification->read_at === null;
            @endphp

            <article
                class="notification-item {{ $isUnread
                    ? 'notification-unread'
                    : '' }}"
            >

                <div class="notification-icon">
                    {{ $isUnread ? '🔔' : '✓' }}
                </div>

                <div class="notification-content">

                    <div class="notification-heading">

                        <strong>
                            {{ $data['title'] ?? 'Notification' }}
                        </strong>

                        <time>
                            {{ $notification->created_at
                                ->diffForHumans() }}
                        </time>

                    </div>

                    <p>
                        {{ $data['message']
                            ?? 'Une nouvelle information est disponible.' }}
                    </p>

                    @if (! empty($data['comment']))
                        <div class="notification-comment">
                            <strong>Observation :</strong>
                            {{ $data['comment'] }}
                        </div>
                    @endif

                    <div class="notification-actions">

                        <a
                            href="{{ route(
                                'notifications.read',
                                $notification
                            ) }}"
                            class="btn-rca-primary btn-small"
                        >
                            Ouvrir
                        </a>

                        <form
                            method="POST"
                            action="{{ route(
                                'notifications.destroy',
                                $notification
                            ) }}"
                        >
                            @csrf
                            @method('DELETE')

                            <button
                                type="submit"
                                class="btn-rca-secondary btn-small"
                            >
                                Supprimer
                            </button>
                        </form>

                    </div>

                </div>

            </article>

        @empty

            <div class="empty-state">
                <div class="empty-state-icon">🔕</div>

                <h2>Aucune notification</h2>

                <p>
                    Les nouvelles informations liées à votre compte
                    apparaîtront ici.
                </p>
            </div>

        @endforelse

        @if ($notifications->hasPages())
            <div class="pagination-wrapper">
                {{ $notifications->links() }}
            </div>
        @endif

    </div>

</section>

@endsection