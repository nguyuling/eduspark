<?php

namespace App\Policies;

use App\Models\Quiz;
use App\Models\User;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Auth;

class QuizPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }



    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quiz $quiz): bool
    {
        // True if the authenticated user's ID matches the quiz's teacher_id
        return $user->id === $quiz->teacher_id;
    }

    public function delete(User $user, Quiz $quiz): bool
    {
        // Option 1: Only allow deletion if the teacher_id on the quiz matches the authenticated user's ID.
        // This is the standard policy for resource ownership.
        return $user->id === $quiz->teacher_id;
        
        // --- OR ---
        
        // Option 2: If you have an Admin/Super User role, you might use a super user check:
        /*
        return $user->role === 'admin' 
            || $user->id === $quiz->teacher_id;
        */
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Quiz $quiz): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Quiz $quiz): bool
    {
        // 1. Always allow the creator/owner to view
        if ($user->id === $quiz->teacher_id) {
            return true;
        }

        // 2. Allow all other teachers to view if the quiz is published.
        if ($user->role === 'teacher' && $quiz->is_published) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Quiz $quiz): bool
    {
        return false;
    }
}
