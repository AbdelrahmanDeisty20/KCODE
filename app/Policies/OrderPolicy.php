<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OrderPolicy
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
        return $user->hasPermissionTo('view_orders');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view_orders');
    }

    public function create(User $user): bool
    {
        return false; // Orders are created by customers via API/checkout
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit_orders');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete_orders');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_orders');
    }
}
