<?php

namespace App\Policies;

use App\Models\Procedure;
use App\Models\User;

class ProcedurePolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return in_array(
            $user->role,
            [
                User::ROLE_ADMIN,
                User::ROLE_RESPONSABLE,
            ],
            true
        );
    }

    public function view(
        User $user,
        Procedure $procedure
    ): bool {
        if ($user->role === User::ROLE_ADMIN) {
            return true;
        }

        return $user->role === User::ROLE_RESPONSABLE
            && (int) $user->ministry_id
                === (int) $procedure->ministry_id;
    }

    public function create(User $user): bool
    {
        return $user->role === User::ROLE_ADMIN;
    }

    public function update(
        User $user,
        Procedure $procedure
    ): bool {
        return $user->role === User::ROLE_ADMIN;
    }

    public function toggle(
        User $user,
        Procedure $procedure
    ): bool {
        return $user->role === User::ROLE_ADMIN;
    }

    public function delete(
        User $user,
        Procedure $procedure
    ): bool {
        return $user->role === User::ROLE_ADMIN;
    }
}