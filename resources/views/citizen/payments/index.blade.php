@extends('layouts.citizen')

@section('title', 'Mes paiements')

@section('content')
<section class="page-section">

    <x-page-header
        title="Mes paiements"
        subtitle="Consultez l’historique de vos transactions."
        kicker="Espace citoyen"
    />

    <x-table-wrapper
        title="Historique des paiements"
        subtitle="Transactions associées à vos demandes administratives."
    >
        <table class="rca-table">
            <thead>
                <tr>
                    <th>Référence</th>
                    <th>Demande</th>
                    <th>Démarche</th>
                    <th>Montant</th>
                    <th>Moyen</th>
                    <th>Statut</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>
                @forelse($payments as $payment)
                    <tr>
                        <td>
                            <strong>{{ $payment->reference }}</strong>
                        </td>

                        <td>{{ $payment->application->reference ?? '-' }}</td>

                        <td>
                            {{ $payment->application->procedure->title ?? '-' }}
                        </td>

                        <td>{{ $payment->formatted_amount }}</td>

                        <td>{{ $payment->method_label }}</td>

                        <td>
                            <x-status-badge
                                :status="$payment->status"
                                :label="$payment->status_label"
                            />
                        </td>

                        <td>
                            {{ $payment->created_at->format('d/m/Y H:i') }}
                        </td>

                        <td>
                            <x-action-button
                                :href="route('citizen.payments.show', $payment)"
                                size="small"
                                variant="warning"
                            >
                                Voir
                            </x-action-button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <x-empty-state
                                icon="💳"
                                title="Aucun paiement"
                                message="Vous n’avez encore initié aucun paiement."
                            />
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <x-slot:pagination>
            {{ $payments->links() }}
        </x-slot:pagination>
    </x-table-wrapper>

</section>
@endsection