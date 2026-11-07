<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserManagementService
{
    /**
     * @param array<string, mixed> $data
     */
    public function createAgent(array $data): User
{
    return DB::transaction(function () use ($data) {
        $agent = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'password' => Hash::make($data['password']),
            'role' => $data['role'] ?? 'agent',
            'ministry_id' => $data['ministry_id'],
            'active' => true,
        ]);

        AuditService::log(
            'creation_agent_ministeriel',
            'User',
            $agent->id,
            [
                'name' => $agent->name,
                'email' => $agent->email,
                'role' => $agent->role,
                'ministry_id' => $agent->ministry_id,
            ]
        );

        return $agent;
    });
}
    public function toggleStatus(User $user): User
    {
        return DB::transaction(function () use ($user) {
            $user->update([
                'active' => ! $user->active,
            ]);

            AuditService::log(
                'Changement de statut',
                'Utilisateur',
                $user->id,
                [
                    'name' => $user->name,
                    'role' => $user->role,
                    'active' => $user->active,
                ]
            );

            return $user->fresh();
        });
    }
}