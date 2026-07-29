<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CouponPolicy
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
        return $user->can('view_coupons');
    }

    public function view(User $user): bool
    {
        return $user->can('view_coupons');
    }

    public function create(User $user): bool
    {
        return $user->can('create_coupons');
    }

    public function update(User $user): bool
    {
        return $user->can('edit_coupons');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_coupons');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_coupons');
    }
}
