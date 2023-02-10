<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Campaign;
use Illuminate\Contracts\View\View;
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
    public function render(): View
    {
        $systemView = \sprintf(
            'components.%s.campaign-metadata',
            $this->campaign->system
        );
        if (view()->exists($systemView)) {
            /** @var View */
            $view = view($systemView);
            return $view;
        }
        /** @var View */
        $view = view('components.campaign-options');
        return $view;
    }
}
