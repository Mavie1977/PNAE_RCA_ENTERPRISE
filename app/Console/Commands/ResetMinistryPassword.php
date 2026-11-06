<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class ResetMinistryPassword extends Command
{
    protected $signature = 'pnae:reset-ministry-password
                            {id : Identifiant du compte}
                            {--apply : Effectuer la réinitialisation}';

    protected $description =
        'Réinitialiser de manière sécurisée le mot de passe d’un agent ou responsable';

    public function handle(): int
    {
        $user = User::query()
            ->whereKey((int) $this->argument('id'))
            ->whereIn('role', ['agent', 'responsable'])
            ->whereNotNull('ministry_id')
            ->first();

        if (! $user) {
            $this->error('Compte ministériel introuvable.');
            return self::FAILURE;
        }

        $this->table(
            ['ID', 'Nom', 'Email', 'Rôle', 'Ministère', 'Actif'],
            [[
                $user->id,
                $user->name,
                $user->email,
                $user->role,
                $user->ministry_id,
                $user->active ? 'Oui' : 'Non',
            ]]
        );

        if (! $this->option('apply')) {
            $this->info('SIMULATION : aucun mot de passe modifié.');
            return self::SUCCESS;
        }

        if (! $this->confirm('Confirmer la réinitialisation ?', false)) {
            $this->warn('Opération annulée.');
            return self::SUCCESS;
        }

        $password = Str::random(24);

        $user->password = Hash::make($password);
        $user->save();

        if (! Hash::check($password, $user->fresh()->password)) {
            $this->error('Échec de la vérification du mot de passe.');
            return self::FAILURE;
        }

        $this->info('Mot de passe réinitialisé et vérifié.');
        $this->warn(
            'Mot de passe temporaire : ' . $password
        );
        $this->warn(
            'Transmettre ce mot de passe uniquement au titulaire par un canal sécurisé.'
        );

        return self::SUCCESS;
    }
}
