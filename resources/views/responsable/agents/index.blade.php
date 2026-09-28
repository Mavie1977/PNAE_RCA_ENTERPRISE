@extends('layouts.responsable')

@section('title', 'Agents de mon ministère')

@section('content')



<section class="decision-dashboard">

    <div class="decision-heading">
        <div>
            <span class="decision-kicker">
                GESTION MINISTÉRIELLE
            </span>

            <h1>Agents de mon ministère</h1>

            <p>
                Consultez les agents affectés à votre ministère.
            </p>
        </div>

        <a
            href="{{ route('responsable.dashboard') }}"
            class="btn-rca-primary"
        >
            Retour à la supervision
        </a>
    </div>

    <article class="decision-panel">

        <form
            method="GET"
            action="{{ route('responsable.agents.index') }}"
            class="user-filters"
        >
            <input
                type="search"
                name="search"
                value="{{ request('search') }}"
                placeholder="Nom, email ou téléphone"
            >

            <select name="active">
                <option value="">Tous les états</option>

                <option
                    value="1"
                    @selected(request('active') === '1')
                >
                    Actifs
                </option>

                <option
                    value="0"
                    @selected(request('active') === '0')
                >
                    Désactivés
                </option>
            </select>

            <button type="submit">
                Rechercher
            </button>
        </form>

        <div class="decision-table-wrapper">

    <table class="decision-table">
        <thead>
            <tr>
                <th>Agent</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Ministère d’affectation</th>
                <th>État</th>
                <th>Date de création</th>

                @if (auth()->user()->isResponsableFonctionPublique())
                    <th>Actions</th>
                @endif
            </tr>
        </thead>

        <tbody>
            @forelse ($agents as $agent)
                <tr>
                    <td>
                        <strong>{{ $agent->name }}</strong>
                    </td>

                    <td>
                        {{ $agent->email }}
                    </td>

                    <td>
                        {{ $agent->phone ?: '—' }}
                    </td>

                    <td>
                        {{ $agent->ministry?->name ?? 'Non affecté' }}
                    </td>

                    <td>
                        <span class="status-badge {{ $agent->active ? 'status-active' : 'status-inactive' }}">
                            {{ $agent->active ? 'Actif' : 'Désactivé' }}
                        </span>
                    </td>

                    <td>
                        @if ($agent->created_at)
                            <strong>
                                {{ $agent->created_at->format('d/m/Y') }}
                            </strong>

                            <br>

                            <small>
                                {{ $agent->created_at->format('H:i') }}
                            </small>
                        @else
                            —
                        @endif
                    </td>

                    @if (auth()->user()->isResponsableFonctionPublique())
               <td>
    <div class="table-actions">
	
	        <a
                 href="{{ route('responsable.agents.show', $agent) }}"
                 class="btn-table-secondary"
             >
                    Dossier RH
           </a>

            <a
                href="{{ route('responsable.recruitment.agents.edit', $agent) }}"
                 class="btn-table-secondary"
             >
                    Modifier
             </a>
			 
             <a
                    href="{{ route(
                          'responsable.recruitment.agents.transfer-form',
                           $agent
                     ) }}"
                        class="btn-table-secondary"
                >
                       Mutation
                    </a>

               <a
                   href="{{ route(
                        'responsable.recruitment.agents.history',
                         $agent
                      ) }}"
                        class="btn-table-secondary"
                  >
                     Historique
                  </a>

        <button
            type="button"
            class="btn-table-warning"
            onclick="document.getElementById(
                'activation-form-{{ $agent->id }}'
            ).hidden = false"
        >
            {{ $agent->active ? 'Désactiver' : 'Activer' }}
        </button>

        <form
            id="activation-form-{{ $agent->id }}"
            method="POST"
            action="{{ route(
                'responsable.recruitment.agents.toggle',
                $agent
            ) }}"
            hidden
            class="activation-reason-form"
        >
            @csrf
            @method('PATCH')

            <label>
                Motif administratif
            </label>

            <textarea
                name="reason"
                required
            ></textarea>

            <label>
                Référence de l’acte
            </label>

            <input
                type="text"
                name="reference"
            >

            <button type="submit">
                Confirmer
            </button>
        </form>

    </div>
</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td
                        colspan="{{ auth()->user()->isResponsableFonctionPublique() ? 7 : 6 }}"
                    >
                        Aucun agent n’est affecté à ce ministère.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

</div>

        @if ($agents->hasPages())
            <div style="margin-top: 18px;">
                {{ $agents->links() }}
            </div>
        @endif

    </article>

</section>

@endsection