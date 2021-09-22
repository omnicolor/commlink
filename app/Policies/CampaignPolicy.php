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
     * @param User $user
     * @return bool
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     * @param User $user
     * @param Campaign $campaign
     * @return bool
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
        // Players invited or accepted have access to it.
        if ($campaign->users->contains($user)) {
            return true;
        }
        return false;
    }

    /**
     * Determine whether the user can create campaigns.
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can GM a campaign.
     * @param User $user
     * @param Campaign $campaign
     * @return bool
     */
    public function gm(User $user, Campaign $campaign): bool
    {
        return $user->id === $campaign->gm;
    }

    /**
     * Determine whether the user can update the model.
     * @param User $user
     * @param Campaign $campaign
     * @return bool
     */
    public function update(User $user, Campaign $campaign): bool
    {
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     * @param User $user
     * @param Campaign $campaign
     * @return bool
     */
    public function delete(User $user, Campaign $campaign): bool
    {
        return false;
    }

    /**
     * Determine whether the user can restore the model.
     * @param User $user
     * @param Campaign $campaign
     * @return bool
     */
    public function restore(User $user, Campaign $campaign): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @param User $user
     * @param Campaign $campaign
     * @return bool
     */
    public function forceDelete(User $user, Campaign $campaign): bool
    {
        return false;
    }
}
