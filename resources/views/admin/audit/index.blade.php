@extends('layouts.admin')

@section('title', 'Journal national')

@section('content')
<section class="page-section">

    <div class="page-heading">
        <h1>Journal national</h1>
        <p>Historique des actions réalisées sur la plateforme.</p>
    </div>

    <div class="table-card">

        <table class="rca-table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Utilisateur</th>
                    <th>Action</th>
                    <th>Entité</th>
                    <th>ID</th>
                    <th>Adresse IP</th>
                </tr>
            </thead>

            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ $log->created_at->format('d/m/Y H:i') }}</td>

                        <td>
                            {{ $log->user->name ?? 'Système' }}
                        </td>

                        <td>
                            <strong>{{ $log->action }}</strong>
                        </td>

                        <td>{{ $log->entity ?? '-' }}</td>

                        <td>{{ $log->entity_id ?? '-' }}</td>

                        <td>{{ $log->ip_address ?? '-' }}</td>
                    </tr>

                    @if(!empty($log->payload))
                        <tr>
                            <td colspan="6">
                                <details>
                                    <summary>Voir les détails</summary>

                                    <pre>{{ json_encode(
                                        $log->payload,
                                        JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
                                    ) }}</pre>
                                </details>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="6" class="text-center">
                            Aucun événement enregistré.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>

    </div>

</section>
@endsection