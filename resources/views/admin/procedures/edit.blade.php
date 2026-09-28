@extends('layouts.admin')

@section('title', 'Modifier une démarche')

@section('content')

<section class="page-section">

    <div class="page-heading">
        <h1>Modifier la démarche</h1>

        <p>
            {{ $procedure->title }}
        </p>
    </div>

    <form
        method="POST"
        action="{{ route(
            'admin.procedures.update',
            $procedure
        ) }}"
        class="enterprise-form-card"
    >
        @csrf
        @method('PUT')

        @include('admin.procedures._form')

        <div class="form-actions">
            <button
                type="submit"
                class="btn-rca-primary"
            >
                Enregistrer les modifications
            </button>

            <a
                href="{{ route(
                    'admin.procedures.show',
                    $procedure
                ) }}"
                class="btn-rca-secondary"
            >
                Annuler
            </a>
        </div>
    </form>

</section>

@endsection