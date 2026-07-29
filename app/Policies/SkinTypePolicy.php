<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SkinTypePolicy
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
        return $user->hasPermissionTo('view_skin_types');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view_skin_types');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_skin_types');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit_skin_types');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete_skin_types');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_skin_types');
    }
}
