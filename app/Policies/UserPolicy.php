<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    public function before(User $authenticatedUser, string $ability): ?bool
    {
        if ($authenticatedUser->isAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $authenticatedUser): bool
    {
        return $authenticatedUser->isResponsable();
    }

    public function view(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        if (! $authenticatedUser->isResponsable()) {
            return false;
        }

        return $targetUser->isAgent()
            && $authenticatedUser->belongsToMinistry(
                $targetUser->ministry_id
            );
    }

    public function create(User $authenticatedUser): bool
    {
        return $authenticatedUser->isResponsable();
    }

    public function update(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        return $this->view($authenticatedUser, $targetUser);
    }

    public function toggle(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        return $this->update($authenticatedUser, $targetUser);
    }

    public function resetPassword(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        return $this->update($authenticatedUser, $targetUser);
    }

    public function delete(
        User $authenticatedUser,
        User $targetUser
    ): bool {
        return $this->update($authenticatedUser, $targetUser);
    }
}