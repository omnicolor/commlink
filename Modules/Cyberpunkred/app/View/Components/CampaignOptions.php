<?php

declare(strict_types=1);

namespace Modules\Cyberpunkred\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * @psalm-suppress UnusedClass
 */
class CampaignOptions extends Component
{
    public function render(): View
    {
        return view('cyberpunkred::components.campaign-options');
    }
}
