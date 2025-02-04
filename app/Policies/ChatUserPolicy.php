<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\ChatUser;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatUserPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view a model.
     */
    public function view(User $user, ChatUser $chat_user): bool
    {
        return $chat_user->user->is($user);
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
    public function update(User $user, ChatUser $chat_user): bool
    {
        return $chat_user->user->is($user);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ChatUser $chat_user): bool
    {
        return $chat_user->user->is($user);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(): bool
    {
        return false;
    }
}
