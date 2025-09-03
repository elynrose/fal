<?php

namespace App\Policies;

use App\Models\PhotoModel;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class PhotoModelPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, PhotoModel $photoModel): bool
    {
        return $user->id === $photoModel->user_id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, PhotoModel $photoModel): bool
    {
        return $user->id === $photoModel->user_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, PhotoModel $photoModel): bool
    {
        return $user->id === $photoModel->user_id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, PhotoModel $photoModel): bool
    {
        return $user->id === $photoModel->user_id;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, PhotoModel $photoModel): bool
    {
        return $user->id === $photoModel->user_id;
    }
}
