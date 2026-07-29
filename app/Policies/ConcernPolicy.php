<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConcernPolicy
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
        return $user->hasPermissionTo('view_concerns');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view_concerns');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_concerns');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit_concerns');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete_concerns');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_concerns');
    }
}
