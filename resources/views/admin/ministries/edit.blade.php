@extends('layouts.admin')

@section('title', 'Modifier un ministère')

@section('content')

<section class="page-section">

    <div class="page-heading">
        <h1>Modifier le ministère</h1>

        <p>
            {{ $ministry->name }}
        </p>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Veuillez corriger les erreurs suivantes :</strong>

            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.ministries.update', $ministry) }}"
        class="enterprise-form-card"
    >
        @csrf
        @method('PUT')

        @include('admin.ministries._form')

        <div class="form-actions">
            <button
                type="submit"
                class="btn-rca-primary"
            >
                Enregistrer les modifications
            </button>

            <a
                href="{{ route('admin.ministries.show', $ministry) }}"
                class="btn-rca-secondary"
            >
                Annuler
            </a>
        </div>
    </form>

</section>

@endsection