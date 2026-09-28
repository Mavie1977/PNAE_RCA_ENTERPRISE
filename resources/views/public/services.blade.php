@extends('layouts.public')

@section('content')

<section class="page-section">

    <h1 class="page-title">Catalogue des services publics</h1>

    <p class="page-subtitle">
        Choisissez une démarche administrative et connectez-vous pour déposer votre demande.
    </p>

    <div class="table-card">

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Ministère</th>
                    <th>Démarche</th>
                    <th>Description</th>
                    <th>Délai</th>
                    <th>Frais</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @foreach($procedures as $procedure)
                    <tr>
                        <td>{{ $procedure->ministry->name ?? 'Ministère concerné' }}</td>
                        <td>
                            <strong>{{ $procedure->title }}</strong>
                        </td>
                        <td>{{ $procedure->description }}</td>
                        <td>{{ $procedure->processing_days ?? '7 jours' }}</td>
                        <td>{{ number_format($procedure->fee, 0, ',', ' ') }} FCFA</td>
                        <td>

    @auth

        <a href="{{ route('citizen.application.create') }}" class="btn-table">
            Déposer
        </a>

    @else

        <a href="{{ route('login') }}" class="btn-table">
            Se connecter pour déposer
        </a>

    @endauth

</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>

</section>

@endsection