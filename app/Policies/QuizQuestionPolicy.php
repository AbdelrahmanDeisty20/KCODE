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
        return $user->can('view_quiz_questions');
    }

    public function view(User $user): bool
    {
        return $user->can('view_quiz_questions');
    }

    public function create(User $user): bool
    {
        return $user->can('create_quiz_questions');
    }

    public function update(User $user): bool
    {
        return $user->can('edit_quiz_questions');
    }

    public function delete(User $user): bool
    {
        return $user->can('delete_quiz_questions');
    }

    public function deleteAny(User $user): bool
    {
        return $user->can('delete_quiz_questions');
    }
}
