<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProductPolicy
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
        return $user->can('view_products');
    }

    public function view(User $user): bool
    {
        return $user->can('view_products');
    }

    public function create(User $user): bool
    {
        return $user->can('create_products');
    }

    public function update(User $user): bool
    {
        return $user->can('edit_products');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_products');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_products');
    }
}
