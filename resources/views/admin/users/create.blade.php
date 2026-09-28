@extends('layouts.admin')

@section('title', 'Créer un utilisateur')

@section('content')

<section class="page-section">
    <div class="page-heading">
        <h1>Créer un utilisateur</h1>
        <p>Attribuez le rôle et le ministère autorisés.</p>
    </div>

    <form
        method="POST"
        action="{{ route('admin.users.store') }}"
        class="enterprise-form-card"
    >
        @csrf

        @include('admin.users._form')

        <div class="form-actions">
            <button type="submit" class="btn-rca-primary">
                Enregistrer le compte
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