<?php

declare(strict_types=1);

namespace App\View\Components\Cyberpunkred;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CampaignOptions extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.cyberpunkred.campaign-options');
    }
}
