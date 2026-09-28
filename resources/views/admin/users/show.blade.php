@extends('layouts.admin')

@section('title', 'Fiche utilisateur')

@section('content')

<section class="page-section">

    <div class="page-heading">
        <h1>{{ $user->name }}</h1>
        <p>Fiche du compte utilisateur.</p>
    </div>

    <div class="enterprise-detail-card">
        <dl>
            <dt>Email</dt>
            <dd>{{ $user->email }}</dd>

            <dt>Téléphone</dt>
            <dd>{{ $user->phone ?: '—' }}</dd>

            <dt>Rôle</dt>
            <dd>{{ \App\Models\User::roles()[$user->role] ?? $user->role }}</dd>

            <dt>Ministère</dt>
            <dd>{{ $user->ministry->name ?? '—' }}</dd>

            <dt>État du compte</dt>
            <dd>{{ $user->active ? 'Actif' : 'Désactivé' }}</dd>

            <dt>Créé le</dt>
            <dd>{{ $user->created_at?->format('d/m/Y H:i') }}</dd>
        </dl>
    </div>

    <div class="user-security-actions">

        <form
            method="POST"
            action="{{ route(
                'admin.users.reset-password',
                $user
            ) }}"
            onsubmit="return confirm(
                'Réinitialiser le mot de passe de ce compte ?'
            )"
        >
            @csrf

            <button type="submit" class="btn-table-warning">
                🔑 Réinitialiser le mot de passe
            </button>
        </form>

        <a
            href="{{ route('admin.users.edit', $user) }}"
            class="btn-rca-primary"
        >
            Modifier
        </a>

        <a
            href="{{ route('admin.users.index') }}"
            class="btn-rca-secondary"
        >
            Retour
        </a>

    </div>

</section>

@endsection