@extends('layouts.responsable')

@section('title', 'Historique de carrière')

@section('content')

<section class="decision-dashboard">

    <div class="decision-heading">
        <div>
            <span class="decision-kicker">
                DOSSIER ADMINISTRATIF
            </span>

            <h1>Historique de carrière</h1>

            <p>
                {{ $agent->name }} —
                {{ $agent->ministry?->name ?? 'Non affecté' }}
            </p>
        </div>

        <a
            href="{{ route('responsable.agents.index') }}"
            class="btn-rca-secondary"
        >
            Retour aux agents
        </a>
    </div>

    <article class="decision-panel">

        <div class="decision-table-wrapper">

            <table class="decision-table">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Événement</th>
                        <th>Ancienne affectation</th>
                        <th>Nouvelle affectation</th>
                        <th>Référence</th>
                        <th>Motif</th>
                        <th>Auteur</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($histories as $history)
                        <tr>
                            <td>
                                <strong>
                                    {{ $history->effective_at?->format(
                                        'd/m/Y'
                                    ) }}
                                </strong>

                                <br>

                                <small>
                                    {{ $history->effective_at?->format(
                                        'H:i'
                                    ) }}
                                </small>
                            </td>

                            <td>
                                {{ $history->title }}
                            </td>

                            <td>
                                {{ $history->fromMinistry?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $history->toMinistry?->name ?? '—' }}
                            </td>

                            <td>
                                {{ $history->reference ?: '—' }}
                            </td>

                            <td>
                                {{ $history->reason ?: '—' }}
                            </td>

                            <td>
                                {{ $history->author?->name ?? 'Système' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                Aucun événement de carrière enregistré.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

        </div>

        @if ($histories->hasPages())
            <div style="margin-top: 20px;">
                {{ $histories->links() }}
            </div>
        @endif

    </article>

</section>

@endsection