<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class CampaignList extends Component
{
    /**
     * Create a new component instance.
     * @param Collection<int|string, Campaign> $gmed
     * @param Collection<int|string, Campaign> $registered
     * @param Collection<int|string, Campaign> $playing
     * @param User $user
     */
    public function __construct(
        public Collection $gmed,
        public Collection $registered,
        public Collection $playing,
        public User $user,
    ) {
        $this->registered = $registered->reject(
            function (Campaign $value) use ($user): bool {
                if ($value->registered_by !== $user->id) {
                    // Registered by another user, shouldn't have been included.
                    return true;
                }
                if (null === $value->gm) {
                    // Registered by the user and there's no GM, don't reject.
                    return false;
                }
                // Reject games where the GM is the person that registered the
                // campaign, so that it will show up as a GMed game, not
                // a registered game.
                return $value->gm === $value->registered_by;
            }
        );
        $this->playing = $playing->reject(
            function (Campaign $value) use ($user): bool {
                return $user->id === $value->registered_by;
            }
        );
    }

    public function render(): View
    {
        return view('components.campaign-list');
    }
}
