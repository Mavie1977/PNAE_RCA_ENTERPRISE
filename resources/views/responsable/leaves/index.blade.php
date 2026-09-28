@extends('layouts.responsable')

@section('title', 'Congés et absences')

@section('content')

<section style="max-width:1150px;margin:0 auto;padding:28px 18px;">

    <div style="
        display:flex;
        justify-content:space-between;
        align-items:center;
        margin-bottom:20px;
    ">

        <div>
            <div style="
                color:#d6002f;
                font-weight:800;
                font-size:11px;
            ">
                DOSSIER RH
            </div>

            <h1 style="
                color:#073b82;
                margin:5px 0;
            ">
                Congés et absences
            </h1>

            <div>
                {{ $agent->name }}
            </div>
        </div>

        <a
            href="{{ route(
                'responsable.agents.leaves.create',
                $agent
            ) }}"
            style="
                background:#ffc900;
                padding:10px 15px;
                text-decoration:none;
                font-weight:800;
                border-radius:7px;
                color:#072e66;
            "
        >
            + Nouveau congé
        </a>

    </div>


    <div style="
        background:white;
        padding:16px;
        border-radius:12px;
        border:1px solid #dae4ef;
    ">

        <table style="
            width:100%;
            border-collapse:collapse;
        ">

            <thead>
                <tr style="
                    background:#0b4ca1;
                    color:white;
                ">
                    <th>Date</th>
                    <th>Type</th>
                    <th>Période</th>
                    <th>Jours</th>
                    <th>Statut</th>
                    <th>Référence</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                @forelse($leaves as $leave)

                    <tr>

                        <td>
                            {{ $leave->created_at->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $leave->type_label }}
                        </td>

                        <td>
                            {{ $leave->start_date->format('d/m/Y') }}
                            →
                            {{ $leave->end_date->format('d/m/Y') }}
                        </td>

                        <td>
                            {{ $leave->days_count }}
                        </td>

                        <td>
                            {{ $leave->status_label }}
                        </td>

                        <td>
                            {{ $leave->reference ?: '—' }}
                        </td>

                        <td>

                            @if($leave->status === 'pending')

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'responsable.agents.leaves.approve',
                                        [$agent, $leave]
                                    ) }}"
                                    style="display:inline;"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <button type="submit">
                                        Valider
                                    </button>
                                </form>

                                <form
                                    method="POST"
                                    action="{{ route(
                                        'responsable.agents.leaves.reject',
                                        [$agent, $leave]
                                    ) }}"
                                    style="display:inline;"
                                >
                                    @csrf
                                    @method('PATCH')

                                    <input
                                        type="hidden"
                                        name="decision_reason"
                                        value="Refus administratif"
                                    >

                                    <button type="submit">
                                        Refuser
                                    </button>
                                </form>

                            @else
                                —
                            @endif

                        </td>

                    </tr>

                @empty

                    <tr>
                        <td colspan="7">
                            Aucun congé ou absence enregistré.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>

    </div>

</section>

@endsection