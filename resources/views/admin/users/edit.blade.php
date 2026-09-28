@extends('layouts.admin')

@section('title', 'Modifier un utilisateur')

@section('content')

<section class="page-section">
    <div class="page-heading">
        <h1>Modifier le compte</h1>
        <p>{{ $user->name }} — {{ $user->email }}</p>
    </div>

    <form
        method="POST"
        action="{{ route('admin.users.update', $user) }}"
        class="enterprise-form-card"
    >
        @csrf
        @method('PUT')

        @include('admin.users._form')

        <div class="form-actions">
            <button type="submit" class="btn-rca-primary">
                Enregistrer les modifications
            </button>

            <a
                href="{{ route('admin.users.index') }}"
                class="btn-rca-secondary"
            >
                Annuler
            </a>
        </div>
    </form>
</section>

@endsection