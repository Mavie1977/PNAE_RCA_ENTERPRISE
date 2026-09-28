@extends('layouts.agent')

@section('title', 'Demandes administratives')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Demandes administratives</h1>
        <p>
            Recherchez, consultez et traitez les dossiers citoyens.
        </p>
    </div>

    <div class="form-card mb-4">
        <form method="GET" action="{{ route('agent.applications') }}">

            <div class="form-grid">
                <div class="form-group">
                    <label for="search">Recherche</label>

                    <input
                        type="text"
                        id="search"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Référence, citoyen, email ou démarche"
                    >
                </div>

                <div class="form-group">
                    <label for="status">Statut</label>

                    <select id="status" name="status">
                        <option value="">Tous les statuts</option>

                        <option
                            value="soumise"
                            @selected(request('status') === 'soumise')
                        >
                            Soumise
                        </option>

                        <option
                            value="en_traitement"
                            @selected(request('status') === 'en_traitement')
                        >
                            En traitement
                        </option>

                        <option
                            value="validee"
                            @selected(request('status') === 'validee')
                        >
                            Validée
                        </option>

                        <option
                            value="rejetee"
                            @selected(request('status') === 'rejetee')
                        >
                            Rejetée
                        </option>

                        <option
                            value="terminee"
                            @selected(request('status') === 'terminee')
                        >
                            Terminée
                        </option>
                    </select>
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-rca-primary">
                    Rechercher
                </button>

                <a
                    href="{{ route('agent.applications') }}"
                    class="btn-rca-secondary"
                >
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <div class="table-card">
        <div class="card-title-row mb-3">
            <h2>Liste des demandes</h2>

            <a
                href="{{ route('agent.dashboard') }}"
                class="btn-rca-secondary"
            >
                Tableau de bord
            </a>
        </div>

        <div class="table-responsive">
            <table class="rca-table">
                <thead>
                    <tr>
                        <th>Référence</th>
                        <th>Citoyen</th>
                        <th>Démarche</th>
                        <th>Ministère</th>
                        <th>Statut</th>
                        <th>Paiement</th>
                        <th>Documents</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse($applications as $application)
                        <tr>
                            <td>
                                <strong>{{ $application->reference }}</strong>
                            </td>

                            <td>
                                {{ $application->user->name ?? '-' }}
                            </td>

                            <td>
                                {{ $application->procedure->title ?? '-' }}
                            </td>

                            <td>
                                {{ $application->procedure->ministry->name ?? '-' }}
                            </td>

                            <td>
                                <span class="badge-status {{ $application->status }}">
                                    {{ ucfirst(str_replace('_', ' ', $application->status)) }}
                                </span>
                            </td>

                            <td>
                                {{ ucfirst(str_replace('_', ' ', $application->payment_status ?? 'en_attente')) }}
                            </td>

                            <td>
                                {{ $application->documents->count() }} pièce(s)
                            </td>

                            <td>
                                {{ $application->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td>
                                <a
                                    href="{{ route('agent.applications.show', $application) }}"
                                    class="btn-table"
                                >
                                    Ouvrir
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">
                                Aucune demande trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $applications->links() }}
        </div>
    </div>

</section>
@endsection