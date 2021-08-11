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
                if (null === $value->gamemaster) {
                    return false;
                }
                return $value->gamemaster !== $value->registered_by;
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
