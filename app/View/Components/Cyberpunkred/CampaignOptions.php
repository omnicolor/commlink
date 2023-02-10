<?php

declare(strict_types=1);

namespace App\View\Components\Cyberpunkred;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CampaignOptions extends Component
{
    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): View
    {
        /** @var View */
        $view = view('components.cyberpunkred.campaign-options');
        return $view;
    }
}
