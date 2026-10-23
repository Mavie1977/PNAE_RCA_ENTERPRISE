<?php

namespace App\Console\Commands;

use App\Models\Ministry;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Throwable;

class CreateMinistryAgent extends Command
{
    protected $signature = 'pnae:create-ministry-agent {--apply : Créer le compte après confirmation}';

    protected $description = 'Prévisualiser ou créer un agent instructeur pour le ministère du Commerce';

    public function handle(): int
    {
        $name = 'Agent Instructeur Commerce';
        $email = 'agent.commerce@pnae-rca.cf';
        $ministryId = 6;

        $ministry = Ministry::find($ministryId);

        if (!$ministry) {
            $this->error('Ministère introuvable.');
            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('Cette adresse email est déjà utilisée. Aucun changement.');
            return self::FAILURE;
        }

        $this->table(
            ['Nom', 'Email', 'Rôle', 'Ministère', 'Statut'],
            [[$name, $email, 'agent', $ministry->name, 'Actif']]
        );

        if (!$this->option('apply')) {
            $this->info('SIMULATION : aucune écriture effectuée.');
            $this->line('Pour créer le compte : php artisan pnae:create-ministry-agent --apply');
            return self::SUCCESS;
        }

        if (!$this->confirm('Confirmer la création de cet agent ?', false)) {
            $this->warn('Opération annulée.');
            return self::SUCCESS;
        }

        // Génération locale d'un secret aléatoire.
        $password = Str::random(32);

        try {
            $user = DB::transaction(function () use (
                $name,
                $email,
                $ministryId,
                $password
            ) {
                return User::create([
                    'name' => $name,
                    'email' => $email,
                    'role' => 'agent',
                    'ministry_id' => $ministryId,
                    'active' => true,
                    'password' => Hash::make($password),
                ]);
            });
        } catch (Throwable $exception) {
            $this->error('Création impossible : ' . $exception->getMessage());
            return self::FAILURE;
        }

        if (!Hash::check($password, $user->password)) {
            $this->error('Échec de la vérification du mot de passe.');
            return self::FAILURE;
        }

        $this->info('Compte créé et mot de passe vérifié.');
        $this->line('ID : ' . $user->id);
        $this->line('Email : ' . $user->email);
        $this->warn('Mot de passe temporaire (à conserver dans un gestionnaire sécurisé) :');
        $this->line($password);
        $this->warn('Ce mot de passe ne sera pas réaffiché.');

        return self::SUCCESS;
    }
}
