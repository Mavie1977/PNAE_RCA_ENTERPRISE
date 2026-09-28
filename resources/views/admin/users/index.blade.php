@extends('layouts.admin')

@section('title', 'Gestion des utilisateurs')

@section('content')

<section class="page-section">

    <div class="page-header">
        <div>
            <span class="page-kicker">GESTION DES ACCÈS</span>

            <h1>
                {{ auth()->user()->isResponsable()
                    ? 'Agents de mon ministère'
                    : 'Utilisateurs de la plateforme' }}
            </h1>

            <p>
                Création, affectation, activation et contrôle des comptes.
            </p>
        </div>

        <a
            href="{{ route('admin.users.create') }}"
            class="btn-rca-primary"
        >
            ➕ Nouvel utilisateur
        </a>
    </div>

    @if(session('temporary_password'))
        <div class="alert alert-warning">
            <strong>Mot de passe temporaire :</strong>
            <code>{{ session('temporary_password') }}</code>

            <p>
                Copiez-le maintenant : il ne sera plus affiché ensuite.
            </p>
        </div>
    @endif

    <div class="table-card">

        <form
            method="GET"
            action="{{ route('admin.users.index') }}"
            class="users-filter-form"
        >
            <input
                type="search"
                name="q"
                value="{{ request('q') }}"
                placeholder="Nom, email ou téléphone"
            >

            @if(auth()->user()->isAdmin())
                <select name="role">
                    <option value="">Tous les rôles</option>

                    @foreach($roles as $value => $label)
                        <option
                            value="{{ $value }}"
                            @selected(request('role') === $value)
                        >
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            @endif

            <select name="active">
                <option value="">Tous les états</option>
                <option value="1" @selected(request('active') === '1')>
                    Actifs
                </option>
                <option value="0" @selected(request('active') === '0')>
                    Désactivés
                </option>
            </select>

            <button type="submit" class="btn-table-primary">
                Rechercher
            </button>
        </form>

        <div class="table-responsive">
            <table class="rca-table">
                <thead>
                    <tr>
                        <th>Utilisateur</th>
                        <th>Rôle</th>
                        <th>Ministère</th>
                        <th>État</th>
                        <th>Création</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>
                                <strong>{{ $user->name }}</strong><br>
                                <small>{{ $user->email }}</small>
                            </td>

                            <td>
                                {{ $roles[$user->role] ?? $user->role }}
                            </td>

                            <td>
                                {{ $user->ministry->name ?? '—' }}
                            </td>

                            <td>
                                <span class="{{ $user->active
                                    ? 'account-active'
                                    : 'account-disabled' }}">
                                    {{ $user->active
                                        ? 'Actif'
                                        : 'Désactivé' }}
                                </span>
                            </td>

                            <td>
                                {{ $user->created_at?->format('d/m/Y') }}
                            </td>

                            <td class="user-actions">
                                <a
                                    href="{{ route(
                                        'admin.users.show',
                                        $user
                                    ) }}"
                                    class="btn-table-secondary"
                                >
                                    Voir
                                </a>

                                <a
                                    href="{{ route(
                                        'admin.users.edit',
                                        $user
                                    ) }}"
                                    class="btn-table-primary"
                                >
                                    Modifier
                                </a>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'admin.users.toggle',
                                        $user
                                    ) }}"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button
                                        type="submit"
                                        class="btn-table-warning"
                                    >
                                        {{ $user->active
                                            ? 'Désactiver'
                                            : 'Activer' }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6">
                                Aucun utilisateur trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>

    </div>

</section>

@endsection