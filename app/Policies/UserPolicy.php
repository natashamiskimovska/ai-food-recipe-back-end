<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

class UserPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function show(User $user): bool
    {
        $authUser = Auth::user();

        if ($authUser->type == User::ADMIN_ROLE) {
            return true;
        }

        return $authUser->id == $user->id;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function index(User $user): bool
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        if ($authUser->type == User::MEMBER_ROLE) {
            return false;
        }

        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        if ($user->type == User::ADMIN_ROLE) {
            return true;
        }

        return $user->id == $model->id;
    }
}
