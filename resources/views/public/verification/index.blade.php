@extends('layouts.app')

@section('title', 'Vérifier un document')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Vérifier un document officiel</h1>

        <p>
            Saisissez le numéro figurant sur le document
            administratif PNAE-RCA.
        </p>
    </div>

    <div class="verification-search-card">

        @if($errors->any())
            <x-alert type="error" title="Vérification impossible">
                <ul class="component-error-list">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert>
        @endif

        <form
            method="POST"
            action="{{ route('verification.search') }}"
            class="verification-search-form"
        >
            @csrf

            <div class="form-group">
                <label for="official_number">
                    Numéro officiel
                </label>

                <input
                    id="official_number"
                    name="official_number"
                    type="text"
                    value="{{ old('official_number') }}"
                    placeholder="Exemple : DOC-2026-URZM5S4BH7"
                    required
                    autofocus
                >
            </div>

            <x-action-button
                type="submit"
                icon="🔎"
            >
                Vérifier le document
            </x-action-button>

        </form>

    </div>

</section>
@endsection