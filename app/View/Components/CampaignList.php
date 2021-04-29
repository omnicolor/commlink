<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class CampaignList extends Component
{
    /**
     * Collection of campaigns the user is GMing.
     * @var Collection
     */
    public Collection $gmed;

    /**
     * Collection of campaigns the user registered.
     * @var Collection
     */
    public Collection $registered;

    /**
     * Create a new component instance.
     * @param Collection $gmed
     * @param Collection $registered
     */
    public function __construct(Collection $gmed, Collection $registered)
    {
        $this->gmed = $gmed;
        $this->registered = $registered;
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
