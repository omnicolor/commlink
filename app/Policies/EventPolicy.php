<?php

declare(strict_types=1);

namespace App\Policies;

use App\Models\Campaign;
use App\Models\Event;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

/**
 * @psalm-suppress UnusedClass
 */
class EventPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view *any* events.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * A user can view an event if:
     * - They created it
     * - They're the GM of the campaign
     * - They're an accepted or invited user of this campaign
     */
    public function view(?User $user, Event $event): bool
    {
        if (null === $user) {
            return false;
        }

        if ($user->id === $event->created_by) {
            // User created the event.
            return true;
        }

        /** @var Campaign */
        $campaign = $event->campaign;
        $gamemaster = $campaign->gamemaster;
        if (null !== $gamemaster && $gamemaster->is($user)) {
            // User is the campaign's GM.
            return true;
        }

        $campaignUsers = $campaign->users;
        if (!$campaignUsers->contains($user)) {
            // User is NOT a player in the campaign.
            return false;
        }

        // @phpstan-ignore-next-line
        $status = $campaignUsers->find($user->id)->pivot->status;
        if ('invited' === $status || 'accepted' === $status) {
            // User is a player (in good standing) in the campaign.
            return true;
        }

        return false;
    }

    /**
     * A user can create an event if:
     * - They're the GM of the campaign the event is going to be attached to
     * - They've been given permission to create events for a campaign
     *
     * Because this policy requires access to an additional model it can't be
     * done in a policy, and is left to the create function to abort if needed.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function create(User $user): bool
    {
        return false;
    }

    /**
     * A user can create an event if:
     * - They're the GM of the campaign the event is going to be attached to
     * - They've been given permission to create events for a campaign (TODO)
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function createForCampaign(User $user, Campaign $campaign): bool
    {
        $gamemaster = $campaign->gamemaster;
        if (null !== $gamemaster && $gamemaster->is($user)) {
            return true;
        }

        return false;
    }

    /**
     * A user can update an event if:
     * - They're the creator of the event
     * - They're the GM of the campaign the event is attached to
     * - They've been given permission to create events for a campaign (TODO)
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function update(User $user, Event $event): bool
    {
        if ($user->id === $event->created_by) {
            return true;
        }

        $gamemaster = $event->campaign->gamemaster;
        if (null !== $gamemaster && $gamemaster->is($user)) {
            return true;
        }

        return false;
    }

    /**
     * A user can soft delete an event if:
     * - They're the creator of the event
     * - They're the GM of the campaign the event is attached to
     * - They've been given permission to create events for a campaign (TODO)
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function delete(User $user, Event $event): bool
    {
        if ($user->id === $event->created_by) {
            return true;
        }

        $gamemaster = $event->campaign->gamemaster;
        if (null !== $gamemaster && $gamemaster->is($user)) {
            return true;
        }

        return false;
    }

    /**
     * A user can restore an event if:
     * - They're the creator of the event
     * - They're the GM of the campaign the event is attached to
     * - They've been given permission to create events for a campaign (TODO)
     * @psalm-suppress PossiblyUnusedMethod
     */
    public function restore(User $user, Event $event): bool
    {
        if ($user->id === $event->created_by) {
            return true;
        }

        $gamemaster = $event->campaign->gamemaster;
        if (null !== $gamemaster && $gamemaster->is($user)) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     * @psalm-suppress PossiblyUnusedMethod
     * @psalm-suppress PossiblyUnusedParam
     */
    public function forceDelete(User $user, Event $event): bool
    {
        return false;
    }
}
