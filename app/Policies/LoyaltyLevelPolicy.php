<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LoyaltyLevelPolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasAnyRole(['admin', 'super_admin']) || $user->type === 'admin') {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_loyalty_levels');
    }

    public function view(User $user): bool
    {
        return $user->can('view_loyalty_levels');
    }

    public function create(User $user): bool
    {
        return $user->can('create_loyalty_levels');
    }

    public function update(User $user): bool
    {
        return $user->can('edit_loyalty_levels');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_loyalty_levels');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_loyalty_levels');
    }
}
