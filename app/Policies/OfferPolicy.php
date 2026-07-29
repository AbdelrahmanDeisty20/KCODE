<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class OfferPolicy
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
        return $user->can('view_offers');
    }

    public function view(User $user): bool
    {
        return $user->can('view_offers');
    }

    public function create(User $user): bool
    {
        return $user->can('create_offers');
    }

    public function update(User $user): bool
    {
        return $user->can('edit_offers');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_offers');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_offers');
    }
}
