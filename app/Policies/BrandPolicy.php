<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrandPolicy
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
        return $user->can('view_brands');
    }

    public function view(User $user): bool
    {
        return $user->can('view_brands');
    }

    public function create(User $user): bool
    {
        return $user->can('create_brands');
    }

    public function update(User $user): bool
    {
        return $user->can('edit_brands');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_brands');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_brands');
    }
}
