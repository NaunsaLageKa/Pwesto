<?php

namespace App\Policies;

use App\Models\Review;
use App\Models\User;

class ReviewPolicy
{
    /**
     * Pwesto admins may run the admin "delete" action (approved workspace → removed from public home only;
     * other rows → soft delete).
     */
    public function delete(User $user, Review $review): bool
    {
        return $user->role === 'admin';
    }

    /**
     * Hub owners may hide a guest workspace review from their own dashboard only.
     */
    public function dismiss(User $user, Review $review): bool
    {
        if ($user->role !== 'hub_owner') {
            return false;
        }

        return $review->feedback_type === 'workspace'
            && $review->status === 'approved'
            && $review->isOwnedByHubOwner((int) $user->id);
    }
}
