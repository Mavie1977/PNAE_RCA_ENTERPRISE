<?php

namespace App\Console\Commands;

use App\Models\AgentProfile;
use App\Models\User;
use App\Services\HumanResources\AgentMatriculeService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillAgentProfiles extends Command
{
    protected $signature = 'rh:backfill-agent-profiles';

    protected $description = 'Crée les dossiers RH manquants pour les agents existants.';

    public function handle(AgentMatriculeService $matriculeService): int
    {
        $agents = User::query()
            ->where('role', User::ROLE_AGENT)
            ->orderBy('id')
            ->get();

        if ($agents->isEmpty()) {
            $this->warn('Aucun agent trouvé.');
            return self::SUCCESS;
        }

        $created = 0;
        $existing = 0;

        $this->info('Agents trouvés : ' . $agents->count());

        foreach ($agents as $agent) {

            if (AgentProfile::where('user_id', $agent->id)->exists()) {
                $this->line(
                    "Déjà présent : {$agent->name}"
                );

                $existing++;
                continue;
            }

            DB::transaction(function () use (
                $agent,
                $matriculeService,
                &$created
            ) {
                $matricule = $matriculeService->generate(
                    $agent->created_at?->year ?? now()->year
                );

                AgentProfile::create([
                    'user_id' => $agent->id,
                    'ministry_id' => $agent->ministry_id,
                    'matricule' => $matricule,

                    'administrative_status' =>
                        $agent->active
                            ? AgentProfile::STATUS_ACTIVE
                            : AgentProfile::STATUS_SUSPENDED,

                    'recruitment_date' =>
                        $agent->created_at?->toDateString(),

                    'appointment_date' =>
                        $agent->created_at?->toDateString(),

                    'hire_reference' => 'REPRISE-HISTORIQUE',

                    'notes' =>
                        'Dossier RH créé automatiquement lors de la mise en place du module RH.',
                ]);

                $this->info(
                    "Créé : {$agent->name} → {$matricule}"
                );

                $created++;
            });
        }

        $this->newLine();

        $this->info("Dossiers créés : {$created}");
        $this->info("Dossiers déjà existants : {$existing}");
        $this->info(
            'Total AgentProfile : ' . AgentProfile::count()
        );

        return self::SUCCESS;
    }
}