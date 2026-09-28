@extends('layouts.admin')

@section('title', 'Fiche démarche')

@section('content')

<section class="page-section">

    <div class="page-heading">
        <h1>Fiche de la démarche</h1>
        <p>{{ $procedure->title }}</p>
    </div>

    @if(session('success'))
        <div class="alert-success mb-4">
            {{ session('success') }}
        </div>
    @endif

    <div class="dashboard-card">

        <div class="card-title-row">
            <h2>Informations générales</h2>
        </div>

        <table class="rca-table">

            <tr>
                <th style="width:260px;">Nom de la démarche</th>
                <td>{{ $procedure->title }}</td>
            </tr>

            <tr>
                <th>Ministère</th>
                <td>{{ $procedure->ministry->name ?? '-' }}</td>
            </tr>

            <tr>
                <th>Slug</th>
                <td>{{ $procedure->slug }}</td>
            </tr>

            <tr>
                <th>Délai officiel</th>
                <td>{{ $procedure->processing_days }} jours</td>
            </tr>

            <tr>
                <th>Frais administratifs</th>
                <td>{{ number_format($procedure->fee,0,',',' ') }} FCFA</td>
            </tr>

            <tr>
                <th>Description</th>
                <td>

                    @if($procedure->description)

                        {!! nl2br(e($procedure->description)) !!}

                    @else

                        -

                    @endif

                </td>
            </tr>

            <tr>
                <th>Documents obligatoires</th>
                <td>

                    @if($procedure->required_documents)

                        {!! nl2br(e($procedure->required_documents)) !!}

                    @else

                        Aucun document renseigné.

                    @endif

                </td>
            </tr>

            <tr>
                <th>Statut</th>

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

            </tr>

            <tr>

                <th>Date de création</th>

                <td>

                    {{ $procedure->created_at->format('d/m/Y H:i') }}

                </td>

            </tr>

            <tr>

                <th>Dernière modification</th>

                <td>

                    {{ $procedure->updated_at->format('d/m/Y H:i') }}

                </td>

            </tr>

        </table>

    </div>


    <div class="form-card mt-4">

        <div class="form-header">

            <h2>Administration</h2>

            <p>
                Activez ou désactivez cette démarche.
            </p>

        </div>

        <div class="form-actions">

    <a
        href="{{ route('admin.procedures.edit', $procedure) }}"
        class="btn-rca-primary"
    >
        ✏️ Modifier la démarche
    </a>

    <form
        method="POST"
        action="{{ route(
            'admin.procedures.toggle',
            $procedure
        ) }}"
    >
        @csrf
        @method('PATCH')

        <button
            type="submit"
            class="btn-rca-warning"
        >
            {{ $procedure->active
                ? 'Désactiver la démarche'
                : 'Activer la démarche' }}
        </button>
    </form>

    <a
        href="{{ route('admin.procedures.index') }}"
        class="btn-rca-secondary"
    >
        Retour à la liste
    </a>

</div>
        </form>

    </div>


    <div class="table-card mt-5">

        <div class="card-title-row">

            <h2>Statistiques</h2>

        </div>

        <table class="rca-table">

            <tr>

                <th style="width:260px;">

                    Nombre de demandes

                </th>

                <td>

                    {{ $procedure->applications()->count() }}

                </td>

            </tr>

            <tr>

                <th>

                    Dossiers validés

                </th>

                <td>

                    {{ $procedure->applications()->where('status','validee')->count() }}

                </td>

            </tr>

            <tr>

                <th>

                    Dossiers en traitement

                </th>

                <td>

                    {{ $procedure->applications()->where('status','en_traitement')->count() }}

                </td>

            </tr>

            <tr>

                <th>

                    Dossiers rejetés

                </th>

                <td>

                    {{ $procedure->applications()->where('status','rejetee')->count() }}

                </td>

            </tr>

        </table>

    </div>

</section>

@endsection