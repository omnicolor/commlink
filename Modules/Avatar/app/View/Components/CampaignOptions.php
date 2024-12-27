<?php

declare(strict_types=1);

namespace Modules\Avatar\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CampaignOptions extends Component
{
    public function render(): View
    {
        return view('avatar::components.campaign-options');
    }
}
