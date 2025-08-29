<?php

namespace App\Policies;

use App\Models\TrainingMaterial;
use App\Models\User;

class TrainingMaterialPolicy
{
    /**
     * Determine whether the user can view any training material
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can view the training material
     */
    public function view(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can create training materials
     */
    public function create(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can update video material
     */
    public function update(User $user): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        // Admin can delete training materials
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can bulk delete models.
     */
    public function bulkDelete(User $user): bool
    {
        // Only admin can perform bulk delete operations
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, TrainingMaterial $material): bool
    {
        // Only admin can restore video materials
        return $user->hasRole('admin');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, TrainingMaterial $material): bool
    {
        // Only admin can force delete video materials without associated users
        return $user->hasRole('admin') && $material->users()->count() === 0;
    }
}
