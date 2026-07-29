<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuizQuestionPolicy
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
        return $user->hasPermissionTo('view_quiz_questions');
    }

    public function view(User $user): bool
    {
        return $user->hasPermissionTo('view_quiz_questions');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create_quiz_questions');
    }

    public function update(User $user): bool
    {
        return $user->hasPermissionTo('edit_quiz_questions');
    }

    public function delete(User $user): bool
    {
        return $user->hasPermissionTo('delete_quiz_questions');
    }

    public function deleteAny(User $user): bool
    {
        return $user->hasPermissionTo('delete_quiz_questions');
    }
}
