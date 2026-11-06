<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class HarmonizeMinistryAccounts extends Command
{
    protected $signature = 'pnae:harmonize-ministry-accounts {--apply : Appliquer les changements après confirmation}';
    protected $description = 'Prévisualiser ou harmoniser cinq intitulés de comptes ministériels sans changer les mots de passe.';

    private const CHANGES = [
        14 => ['old' => 'Agent Test Carrière', 'new' => 'Agent Instructeur Finances', 'role' => 'agent', 'ministry_id' => 3],
        9 => ['old' => 'Agent Test Audit', 'new' => 'Agent Instructeur Santé', 'role' => 'agent', 'ministry_id' => 5],
        3 => ['old' => 'Agent Public', 'new' => 'Agent Public Santé', 'role' => 'agent', 'ministry_id' => 5],
        2 => ['old' => 'Responsable National', 'new' => 'Responsable Ministère Santé', 'role' => 'responsable', 'ministry_id' => 5],
        15 => ['old' => 'Agent RH Automatique', 'new' => 'Agent Instructeur Commerce', 'role' => 'agent', 'ministry_id' => 6],
    ];

    public function handle(): int
    {
        $rows = [];
        $errors = [];
        foreach (self::CHANGES as $id => $change) {
            $user = User::find($id);
            if (!$user) {
                $errors[] = "Compte #{$id} introuvable";
                continue;
            }
            if ($user->role !== $change['role'] || (int) $user->ministry_id !== $change['ministry_id']) {
                $errors[] = "Compte #{$id} : rôle ou ministère inattendu";
                continue;
            }
            if ($user->name !== $change['old'] && $user->name !== $change['new']) {
                $errors[] = "Compte #{$id} : nom actuel inattendu : {$user->name}";
                continue;
            }
            $rows[] = [$id, $user->email, $user->name, $change['new'], $user->name === $change['new'] ? 'Déjà conforme' : 'À modifier'];
        }

        $this->table(['ID', 'Email', 'Nom actuel', 'Nom cible', 'Action'], $rows);
        if ($errors) {
            foreach ($errors as $error) {
                $this->error($error);
            }
            $this->error('Aucune modification effectuée : vérification préalable échouée.');
            return self::FAILURE;
        }
        if (!$this->option('apply')) {
            $this->info('SIMULATION : aucune écriture effectuée.');
            return self::SUCCESS;
        }
        if (!$this->confirm('Confirmer les 5 intitulés ci-dessus ? Les mots de passe restent inchangés.', false)) {
            $this->warn('Annulé : aucune modification effectuée.');
            return self::SUCCESS;
        }
        DB::transaction(function (): void {
            foreach (self::CHANGES as $id => $change) {
                $user = User::query()->lockForUpdate()->findOrFail($id);
                if ($user->role !== $change['role'] || (int) $user->ministry_id !== $change['ministry_id']) {
                    throw new RuntimeException("Compte #{$id} : rôle/ministère modifié entre-temps.");
                }
                if ($user->name === $change['new']) {
                    continue;
                }
                if ($user->name !== $change['old']) {
                    throw new RuntimeException("Compte #{$id} : nom modifié entre-temps.");
                }
                $user->name = $change['new'];
                $user->save();
            }
        });
        $this->info('Harmonisation terminée. Aucun mot de passe modifié.');
        return self::SUCCESS;
    }
}
