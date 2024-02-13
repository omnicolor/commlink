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
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'CampaignOptions';
    }

    public function render(): View
    {
        $systemView = \sprintf(
            'components.%s.campaign-metadata',
            $this->campaign->system
        );
        if (view()->exists($systemView)) {
            return view($systemView);
        }
        return view('components.campaign-options');
    }
}
