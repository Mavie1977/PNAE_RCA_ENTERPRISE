<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MinistryProcessingAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            [
                'name' => 'Responsable Intérieur',
                'email' => 'responsable.interieur@pnae-rca.cf',
                'role' => 'responsable',
                'ministry_id' => 1,
            ],
            [
                'name' => 'Agent Instructeur Fonction Publique',
                'email' => 'agent.fp@pnae-rca.cf',
                'role' => 'agent',
                'ministry_id' => 2,
            ],
            [
                'name' => 'Responsable Finances',
                'email' => 'responsable.finances@pnae-rca.cf',
                'role' => 'responsable',
                'ministry_id' => 3,
            ],
            [
                'name' => 'Responsable Éducation',
                'email' => 'responsable.education@pnae-rca.cf',
                'role' => 'responsable',
                'ministry_id' => 4,
            ],
            [
                'name' => 'Responsable Commerce',
                'email' => 'responsable.commerce@pnae-rca.cf',
                'role' => 'responsable',
                'ministry_id' => 6,
            ],
            [
                'name' => 'Responsable Affaires Étrangères',
                'email' => 'responsable.affaires@pnae-rca.cf',
                'role' => 'responsable',
                'ministry_id' => 8,
            ],
        ];

        foreach ($accounts as $account) {
            if (DB::table('users')->where('email', $account['email'])->exists()) {
                $this->command->warn(
                    "Compte déjà existant : {$account['email']}"
                );

                continue;
            }

            if (!DB::table('ministries')->where('id', $account['ministry_id'])->exists()) {
                $this->command->error(
                    "Ministère introuvable : {$account['ministry_id']}"
                );

                continue;
            }

            $temporaryPassword = Str::password(20);

            DB::table('users')->insert([
                'name' => $account['name'],
                'email' => $account['email'],
                'password' => Hash::make($temporaryPassword),
                'role' => $account['role'],
                'ministry_id' => $account['ministry_id'],
                'active' => false,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $this->command->info(
                "Compte créé (inactif) : {$account['email']}"
            );

            $this->command->line(
                "Mot de passe temporaire : {$temporaryPassword}"
            );
        }
    }
}