<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
        return $user->hasPermissionTo('view_products');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view_products');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_products');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit_products');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete_products');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_products');
    }
}
