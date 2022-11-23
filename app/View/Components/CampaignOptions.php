<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Campaign;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\View\Component;

class CampaignOptions extends Component
{
    public function __construct(public Campaign $campaign)
    {
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): view
    {
        $systemView = \sprintf(
            'components.%s.campaign-metadata',
            $this->campaign->system
        );
        if (ViewFacade::exists($systemView)) {
            return view($systemView);
        }
        return view('components.campaign-options');
    }
}
