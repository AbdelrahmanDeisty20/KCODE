<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function before(User $user, string $ability): ?bool
    {
        if ($user->type === 'admin' || $user->hasRole('super_admin')) {
            return true;
        }
        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('view_roles');
    }

    public function view(User $user): bool
    {
        return $user->can('view_roles');
    }

    public function create(User $user): bool
    {
        return $user->can('create_roles');
    }

    public function update(User $user): bool
    {
        return $user->can('edit_roles');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_roles');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_roles');
    }
}
