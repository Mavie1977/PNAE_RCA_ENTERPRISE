<?php

namespace App\Services\HumanResources;

use App\Models\AgentCareerHistory;
use App\Models\AgentProfile;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\AgentAdvancement;

class AgentCareerService
{
    public function __construct(
        private readonly AgentMatriculeService $matriculeService
    ) {
    }

    /**
     * Recrutement complet d'un agent.
     *
     * Création :
     * - User
     * - AgentProfile
     * - Matricule
     * - Historique recrutement
     * - Historique affectation initiale
     */
    public function recruit(
        array $agentData,
        User $performedBy,
        ?string $reason = null,
        ?string $reference = null
    ): User {
        return DB::transaction(function () use (
            $agentData,
            $performedBy,
            $reason,
            $reference
        ): User {

            /*
            |--------------------------------------------------------------------------
            | 1. Création du compte utilisateur
            |--------------------------------------------------------------------------
            */

            $agent = User::create([
                'name' => $agentData['name'],
                'email' => $agentData['email'],
                'phone' => $agentData['phone'] ?? null,
                'password' => $agentData['password'],
                'role' => User::ROLE_AGENT,
                'active' => true,
                'ministry_id' => $agentData['ministry_id'],
            ]);

            /*
            |--------------------------------------------------------------------------
            | 2. Génération du matricule national
            |--------------------------------------------------------------------------
            */

            $recruitmentDate =
                $agentData['recruitment_date'] ?? now()->toDateString();

            $year = (int) substr(
                (string) $recruitmentDate,
                0,
                4
            );

            $matricule = $this->matriculeService->generate($year);

            /*
            |--------------------------------------------------------------------------
            | 3. Création du dossier RH
            |--------------------------------------------------------------------------
            */

            $profile = AgentProfile::create([
                'user_id' => $agent->id,

                'ministry_id' => $agent->ministry_id,

                'matricule' => $matricule,

                'service' =>
                    $agentData['service'] ?? null,

                'job_title' =>
                    $agentData['job_title'] ?? null,

                'grade' =>
                    $agentData['grade'] ?? null,

                'category' =>
                    $agentData['category'] ?? null,

                'administrative_status' =>
                    AgentProfile::STATUS_ACTIVE,

                'recruitment_date' =>
                    $recruitmentDate,

                'appointment_date' =>
                    $agentData['appointment_date']
                    ?? $recruitmentDate,

                'hire_reference' =>
                    $reference,

                'birth_date' =>
                    $agentData['birth_date'] ?? null,

                'place_of_birth' =>
                    $agentData['place_of_birth'] ?? null,

                'notes' =>
                    $agentData['notes'] ?? null,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 4. Historique : recrutement
            |--------------------------------------------------------------------------
            */

            AgentCareerHistory::create([
                'agent_id' => $agent->id,

                'performed_by' => $performedBy->id,

                'from_ministry_id' => null,

                'to_ministry_id' =>
                    $agent->ministry_id,

                'event_type' =>
                    AgentCareerHistory::TYPE_RECRUITMENT,

                'title' =>
                    'Recrutement de l’agent',

                'reason' =>
                    $reason,

                'reference' =>
                    $reference,

                'previous_active' =>
                    null,

                'new_active' =>
                    true,

                'metadata' => [
                    'matricule' =>
                        $profile->matricule,

                    'name' =>
                        $agent->name,

                    'email' =>
                        $agent->email,

                    'role' =>
                        $agent->role,

                    'administrative_status' =>
                        $profile->administrative_status,
                ],

                'effective_at' =>
                    $recruitmentDate,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 5. Historique : affectation initiale
            |--------------------------------------------------------------------------
            */

            AgentCareerHistory::create([
                'agent_id' =>
                    $agent->id,

                'performed_by' =>
                    $performedBy->id,

                'from_ministry_id' =>
                    null,

                'to_ministry_id' =>
                    $agent->ministry_id,

                'event_type' =>
                    AgentCareerHistory::TYPE_INITIAL_ASSIGNMENT,

                'title' =>
                    'Affectation initiale',

                'reason' =>
                    $reason,

                'reference' =>
                    $reference,

                'previous_active' =>
                    null,

                'new_active' =>
                    true,

                'metadata' => [
                    'matricule' =>
                        $profile->matricule,
                ],

                'effective_at' =>
                    $agentData['appointment_date']
                    ?? $recruitmentDate,
            ]);

            /*
            |--------------------------------------------------------------------------
            | 6. Retour du nouvel agent
            |--------------------------------------------------------------------------
            */

            return $agent->load([
                'ministry',
                'agentProfile',
            ]);
        });
    }


    /**
     * Mutation d'un agent vers un autre ministère.
     *
     * Met à jour simultanément :
     * - users.ministry_id
     * - agent_profiles.ministry_id
     * - historique
     */
    public function transfer(
        User $agent,
        int $newMinistryId,
        User $performedBy,
        string $reason,
        ?string $reference = null,
        mixed $effectiveAt = null
    ): User {
        return DB::transaction(function () use (
            $agent,
            $newMinistryId,
            $performedBy,
            $reason,
            $reference,
            $effectiveAt
        ): User {

            abort_unless(
                $agent->role === User::ROLE_AGENT,
                404,
                'Le compte sélectionné n’est pas un agent.'
            );

            $oldMinistryId = $agent->ministry_id;

            abort_if(
                $oldMinistryId === $newMinistryId,
                422,
                'Le nouveau ministère doit être différent du ministère actuel.'
            );

            /*
            |--------------------------------------------------------------------------
            | Mise à jour du compte
            |--------------------------------------------------------------------------
            */

            $agent->update([
                'ministry_id' => $newMinistryId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Mise à jour du dossier RH
            |--------------------------------------------------------------------------
            */

            $agent->agentProfile?->update([
                'ministry_id' => $newMinistryId,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Historique
            |--------------------------------------------------------------------------
            */

            AgentCareerHistory::create([
                'agent_id' =>
                    $agent->id,

                'performed_by' =>
                    $performedBy->id,

                'from_ministry_id' =>
                    $oldMinistryId,

                'to_ministry_id' =>
                    $newMinistryId,

                'event_type' =>
                    AgentCareerHistory::TYPE_TRANSFER,

                'title' =>
                    'Mutation ministérielle',

                'reason' =>
                    $reason,

                'reference' =>
                    $reference,

                'previous_active' =>
                    $agent->active,

                'new_active' =>
                    $agent->active,

                'metadata' => [
                    'matricule' =>
                        $agent->agentProfile?->matricule,
                ],

                'effective_at' =>
                    $effectiveAt ?: now(),
            ]);

            return $agent->refresh()->load([
                'ministry',
                'agentProfile',
            ]);
        });
    }


    /**
     * Activation / désactivation d'un agent.
     *
     * Synchronise :
     * - users.active
     * - agent_profiles.administrative_status
     * - historique
     */
    public function changeActivation(
        User $agent,
        bool $active,
        User $performedBy,
        string $reason,
        ?string $reference = null
    ): User {
        return DB::transaction(function () use (
            $agent,
            $active,
            $performedBy,
            $reason,
            $reference
        ): User {

            abort_unless(
                $agent->role === User::ROLE_AGENT,
                404,
                'Le compte sélectionné n’est pas un agent.'
            );

            $previousActive = (bool) $agent->active;

            abort_if(
                $previousActive === $active,
                422,
                $active
                    ? 'Cet agent est déjà actif.'
                    : 'Cet agent est déjà désactivé.'
            );

            /*
            |--------------------------------------------------------------------------
            | Compte utilisateur
            |--------------------------------------------------------------------------
            */

            $agent->update([
                'active' => $active,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Situation RH
            |--------------------------------------------------------------------------
            */

            $agent->agentProfile?->update([
                'administrative_status' =>
                    $active
                        ? AgentProfile::STATUS_ACTIVE
                        : AgentProfile::STATUS_SUSPENDED,
            ]);

            /*
            |--------------------------------------------------------------------------
            | Historique
            |--------------------------------------------------------------------------
            */

            AgentCareerHistory::create([
                'agent_id' =>
                    $agent->id,

                'performed_by' =>
                    $performedBy->id,

                'from_ministry_id' =>
                    $agent->ministry_id,

                'to_ministry_id' =>
                    $agent->ministry_id,

                'event_type' =>
                    $active
                        ? AgentCareerHistory::TYPE_ACTIVATION
                        : AgentCareerHistory::TYPE_DEACTIVATION,

                'title' =>
                    $active
                        ? 'Réactivation du compte agent'
                        : 'Désactivation du compte agent',

                'reason' =>
                    $reason,

                'reference' =>
                    $reference,

                'previous_active' =>
                    $previousActive,

                'new_active' =>
                    $active,

                'metadata' => [
                    'matricule' =>
                        $agent->agentProfile?->matricule,

                    'administrative_status' =>
                        $active
                            ? AgentProfile::STATUS_ACTIVE
                            : AgentProfile::STATUS_SUSPENDED,
                ],

                'effective_at' =>
                    now(),
            ]);

            return $agent->refresh()->load([
                'ministry',
                'agentProfile',
            ]);
        });
    }
	
	public function updateProfile(
    User $agent,
    array $data,
    User $performedBy,
    ?string $reason = null
): User {
    return DB::transaction(function () use (
        $agent,
        $data,
        $performedBy,
        $reason
    ): User {

        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404,
            'Le compte sélectionné n’est pas un agent.'
        );

        $agent->loadMissing('agentProfile');

        $profile = $agent->agentProfile;

        abort_unless(
            $profile,
            422,
            'Aucun dossier RH n’est associé à cet agent.'
        );

        $before = [
            'name' => $agent->name,
            'email' => $agent->email,
            'phone' => $agent->phone,
            'service' => $profile->service,
            'job_title' => $profile->job_title,
            'grade' => $profile->grade,
            'category' => $profile->category,
            'birth_date' => $profile->birth_date?->format('Y-m-d'),
            'place_of_birth' => $profile->place_of_birth,
            'recruitment_date' => $profile->recruitment_date?->format('Y-m-d'),
            'appointment_date' => $profile->appointment_date?->format('Y-m-d'),
            'notes' => $profile->notes,
			'service_id' => $profile->service_id,
            'category_id' => $profile->category_id,
            'grade_id' => $profile->grade_id,
            'function_id' => $profile->function_id,
        ];

        $agent->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
        ]);

        $profile->update([
            'service' => $data['service'] ?? null,
            'job_title' => $data['job_title'] ?? null,
            'grade' => $data['grade'] ?? null,
            'category' => $data['category'] ?? null,
            'birth_date' => $data['birth_date'] ?? null,
            'place_of_birth' => $data['place_of_birth'] ?? null,
            'recruitment_date' => $data['recruitment_date'] ?? null,
            'appointment_date' => $data['appointment_date'] ?? null,
            'notes' => $data['notes'] ?? null,
			'service_id' => $data['service_id'] ?? null,
			'category_id' => $data['category_id'] ?? null,
            'grade_id' => $data['grade_id'] ?? null,
            'function_id' => $data['function_id'] ?? null,
        ]);

        $after = [
            'name' => $agent->fresh()->name,
            'email' => $agent->fresh()->email,
            'phone' => $agent->fresh()->phone,
            'service' => $profile->fresh()->service,
            'job_title' => $profile->fresh()->job_title,
            'grade' => $profile->fresh()->grade,
            'category' => $profile->fresh()->category,
            'birth_date' => $profile->fresh()->birth_date?->format('Y-m-d'),
            'place_of_birth' => $profile->fresh()->place_of_birth,
            'recruitment_date' => $profile->fresh()->recruitment_date?->format('Y-m-d'),
            'appointment_date' => $profile->fresh()->appointment_date?->format('Y-m-d'),
            'notes' => $profile->fresh()->notes,
			'service_id' => $profile->fresh()->service_id,
            'category_id' => $profile->fresh()->category_id,
            'grade_id' => $profile->fresh()->grade_id,
            'function_id' => $profile->fresh()->function_id,
        ];

        AgentCareerHistory::create([
            'agent_id' => $agent->id,
            'performed_by' => $performedBy->id,
            'from_ministry_id' => $agent->ministry_id,
            'to_ministry_id' => $agent->ministry_id,
            'event_type' => AgentCareerHistory::TYPE_PROFILE_UPDATE,
            'title' => 'Mise à jour du dossier RH',
            'reason' => $reason ?: 'Mise à jour administrative du dossier de l’agent.',
            'reference' => null,
            'previous_active' => $agent->active,
            'new_active' => $agent->active,
            'metadata' => [
                'matricule' => $profile->matricule,
                'before' => $before,
                'after' => $after,
            ],
            'effective_at' => now(),
        ]);

        return $agent->refresh()->load([
            'ministry',
            'agentProfile',
        ]);
    });
}
public function advance(
    User $agent,
    array $data,
    User $performedBy
): User {
    return DB::transaction(function () use (
        $agent,
        $data,
        $performedBy
    ): User {

        abort_unless(
            $agent->role === User::ROLE_AGENT,
            404,
            'Le compte sélectionné n’est pas un agent.'
        );

        $agent->loadMissing([
            'agentProfile.categoryEntity',
            'agentProfile.gradeEntity',
            'agentProfile.hrFunction',
        ]);

        $profile = $agent->agentProfile;

        abort_unless(
            $profile,
            422,
            'Aucun dossier RH n’est associé à cet agent.'
        );

        $oldCategoryId = $profile->category_id;
        $oldGradeId = $profile->grade_id;
        $oldFunctionId = $profile->function_id;

        $newCategoryId = (int) $data['new_category_id'];
        $newGradeId = (int) $data['new_grade_id'];

        $newFunctionId = !empty($data['new_function_id'])
            ? (int) $data['new_function_id']
            : $oldFunctionId;

        /*
        |--------------------------------------------------------------------------
        | 1. Historique spécialisé
        |--------------------------------------------------------------------------
        */

        AgentAdvancement::create([
            'agent_id' => $agent->id,
            'performed_by' => $performedBy->id,

            'old_category_id' => $oldCategoryId,
            'new_category_id' => $newCategoryId,

            'old_grade_id' => $oldGradeId,
            'new_grade_id' => $newGradeId,

            'old_function_id' => $oldFunctionId,
            'new_function_id' => $newFunctionId,

            'advancement_type' =>
                $data['advancement_type'],

            'reference' =>
                $data['reference'] ?? null,

            'reason' =>
                $data['reason'] ?? null,

            'effective_at' =>
                $data['effective_at'],
        ]);

        /*
        |--------------------------------------------------------------------------
        | 2. Situation RH actuelle
        |--------------------------------------------------------------------------
        */

        $profile->update([
            'category_id' => $newCategoryId,
            'grade_id' => $newGradeId,
            'function_id' => $newFunctionId,
        ]);

        /*
        |--------------------------------------------------------------------------
        | 3. Historique général de carrière
        |--------------------------------------------------------------------------
        */

        $eventType = match ($data['advancement_type']) {
            AgentAdvancement::TYPE_PROMOTION =>
                AgentCareerHistory::TYPE_PROMOTION,

            AgentAdvancement::TYPE_RECLASSIFICATION =>
                AgentCareerHistory::TYPE_RECLASSIFICATION,

            default =>
                AgentCareerHistory::TYPE_ADVANCEMENT,
        };

        $title = match ($data['advancement_type']) {
            AgentAdvancement::TYPE_PROMOTION =>
                'Promotion de l’agent',

            AgentAdvancement::TYPE_RECLASSIFICATION =>
                'Reclassement de l’agent',

            default =>
                'Avancement de l’agent',
        };

        AgentCareerHistory::create([
            'agent_id' =>
                $agent->id,

            'performed_by' =>
                $performedBy->id,

            'from_ministry_id' =>
                $agent->ministry_id,

            'to_ministry_id' =>
                $agent->ministry_id,

            'event_type' =>
                $eventType,

            'title' =>
                $title,

            'reason' =>
                $data['reason'] ?? null,

            'reference' =>
                $data['reference'] ?? null,

            'previous_active' =>
                $agent->active,

            'new_active' =>
                $agent->active,

            'metadata' => [
                'old_category_id' =>
                    $oldCategoryId,

                'new_category_id' =>
                    $newCategoryId,

                'old_grade_id' =>
                    $oldGradeId,

                'new_grade_id' =>
                    $newGradeId,

                'old_function_id' =>
                    $oldFunctionId,

                'new_function_id' =>
                    $newFunctionId,

                'matricule' =>
                    $profile->matricule,
            ],

            'effective_at' =>
                $data['effective_at'],
        ]);

        return $agent->refresh()->load([
            'ministry',
            'agentProfile.categoryEntity',
            'agentProfile.gradeEntity',
            'agentProfile.hrFunction',
        ]);
    });
}
}