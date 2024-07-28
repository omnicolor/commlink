<?php

declare(strict_types=1);

namespace Modules\Subversion\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * @psalm-suppress UnusedClass
 */
class CampaignOptions extends Component
{
    public function render(): View
    {
        return view('subversion::components.campaign-options');
    }
}
