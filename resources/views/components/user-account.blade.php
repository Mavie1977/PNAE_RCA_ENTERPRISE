@props([
    'dashboardRoute',
    'roleLabel' => null,
])

@php
    $connectedUser = auth()->user();
@endphp

@if($connectedUser)
    <a href="{{ route($dashboardRoute) }}" class="nav-space-link">
        Mon espace
    </a>

    <div class="connected-user-box">
        <span class="connected-user-role">
            {{ $roleLabel ?? strtoupper($connectedUser->role) }}
        </span>

        <strong>{{ $connectedUser->name }}</strong>
        <small>{{ $connectedUser->email }}</small>
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
@endif