<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CampaignPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function view(User $user, Campaign $campaign): bool
    {
        // Users that register a campaign always have access to it.
        if ($user->id === $campaign->registered_by) {
            return true;
        }
        // The game master has access to it.
        if ($user->id === $campaign->gm) {
            return true;
        }
        // Reject users that aren't players of the game.
        if (!$campaign->users->contains($user)) {
            return false;
        }

        /** @var User */
        $player = $campaign->users->find($user->id);
        return 'invited' === $player->pivot->status
            || 'accepted' === $player->pivot->status;
    }

    /**
     * Determine whether the user can invite another user to the game.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function invite(User $user, Campaign $campaign): bool
    {
        return $user->id === $campaign->registered_by
            || $user->id === $campaign->gm;
    }

    /**
     * Determine whether the user can create campaigns.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can GM a campaign.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function gm(User $user, Campaign $campaign): bool
    {
        return $user->id === $campaign->gm;
    }

    /**
     * Determine whether the user can update the model.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function update(User $user, Campaign $campaign): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        return $user->is($campaign->gamemaster)
            || $user->is($campaign->registrant);
    }

    /**
     * Determine whether the user can restore the model.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function restore(User $user, Campaign $campaign): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function forceDelete(User $user, Campaign $campaign): bool
    {
        return false;
    }
}
