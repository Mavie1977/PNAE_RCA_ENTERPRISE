@can('resetPassword', $user)
<div class="dashboard-card mt-4 mb-4">

    <h2>Réinitialisation du mot de passe</h2>

    <p>
        Compte concerné :
        <strong>{{ $user->name }}</strong>
        ({{ $user->email }})
    </p>

    <p>
        Un nouveau mot de passe temporaire sera généré.
        L'ancien mot de passe ne permettra plus de se connecter.
    </p>

    @if(session('temporary_password'))
        <div class="alert alert-warning" role="alert">
            <strong>Nouveau mot de passe temporaire :</strong>

            <code>{{ session('temporary_password') }}</code>

            <p class="mt-2 mb-0">
                Copiez-le maintenant et transmettez-le
                au titulaire du compte par un canal sécurisé.
            </p>
        </div>
    @endif

    <form
        method="POST"
        action="{{ route('admin.users.reset-password', $user) }}"
        onsubmit="return confirm('Confirmer la réinitialisation du mot de passe de cet utilisateur ?');"
    >
        @csrf

        <button type="submit" class="btn-rca-secondary">
            Réinitialiser le mot de passe
        </button>
    </form>

</div>
@endcan