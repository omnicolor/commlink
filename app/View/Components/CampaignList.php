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
    public Collection $registered;

    /**
     * Create a new component instance.
     * @param Collection $gmed
     * @param Collection $registered
     * @param Collection $playing
     * @param User $user
     */
    public function __construct(
        public Collection $gmed,
        Collection $registered,
        public Collection $playing,
        public User $user,
    ) {
        $this->registered = $registered->reject(
            function (Campaign $value, int $key): bool {
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
    }

    /**
     * Get the view / contents that represent the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.campaign-list');
    }
}
