@extends('layouts.admin')

@section('title', 'Gestion des démarches')

@section('content')

<section class="page-section">

    <div class="page-heading">

        <h1>Gestion des démarches</h1>

        <p>
            Configurez les démarches administratives disponibles
            sur la Plateforme Nationale d’Administration Électronique.
        </p>

    </div>

    @if(session('success'))

        <div class="alert-success mb-4">

            {{ session('success') }}

        </div>

    @endif


    <div class="table-card">

        <div class="card-title-row">

            <h2>Liste des démarches</h2>

            <a
                href="{{ route('admin.procedures.create') }}"
                class="btn-rca-primary">

                Nouvelle démarche

            </a>

        </div>

        <table class="rca-table">

            <thead>

                <tr>

                    <th>DÉMARCHE</th>

                    <th>MINISTÈRE</th>

                    <th>DÉLAI</th>

                    <th>FRAIS</th>

                    <th>STATUT</th>

                    <th>DATE</th>

                    <th>ACTION</th>

                </tr>

            </thead>

            <tbody>

            @forelse($procedures as $procedure)

                <tr>

                    <td>

                        <strong>

                            {{ $procedure->title }}

                        </strong>

                    </td>

                    <td>

                        {{ $procedure->ministry->name ?? '-' }}

                    </td>

                    <td>

                        {{ $procedure->processing_days }}

                        jours

                    </td>

                    <td>

                        {{ number_format($procedure->fee,0,',',' ') }}

                        FCFA

                    </td>

                    <td>

                        @if($procedure->active)

                            <span class="badge-status validee">

                                Active

                            </span>

                        @else

                            <span class="badge-status rejetee">

                                Inactive

                            </span>

                        @endif

                    </td>

                    <td>

                        {{ $procedure->created_at->format('d/m/Y') }}

                    </td>

                    <td>

                        <a

                            href="{{ route('admin.procedures.show',$procedure) }}"

                            class="btn-table">

                            Voir

                        </a>

                    </td>

                </tr>

            @empty

                <tr>

                    <td colspan="7" class="text-center">

                        Aucune démarche enregistrée.

                    </td>

                </tr>

            @endforelse

            </tbody>

        </table>

    </div>


    <div class="dashboard-card mt-5">

        <h2>

            Résumé

        </h2>

        <div class="stats-grid">

            <div class="stat-card">

                <div class="stat-title">

                    Total démarches

                </div>

                <div class="stat-number">

                    {{ $procedures->count() }}

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-title">

                    Actives

                </div>

                <div class="stat-number">

                    {{ $procedures->where('active',true)->count() }}

                </div>

            </div>

            <div class="stat-card">

                <div class="stat-title">

                    Inactives

                </div>

                <div class="stat-number">

                    {{ $procedures->where('active',false)->count() }}

                </div>

            </div>

        </div>

    </div>

</section>

@endsection