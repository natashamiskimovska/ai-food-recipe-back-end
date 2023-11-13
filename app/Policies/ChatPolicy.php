<?php

namespace App\Policies;

use App\Models\Chat;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ChatPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function chat(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function index(User $user): bool
    {
        return true;
    }


    /**
     * Determine whether the user can view the model.
     */
    public function show(Chat $chat): bool
    {
        /** @var User $authUser */
        $authUser = Auth::user();

        if ($authUser->type == User::MEMBER_ROLE) {
            return $authUser->id == $chat->user_id;
        }

        return true;
    }
}
