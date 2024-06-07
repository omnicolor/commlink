<?php

declare(strict_types=1);

namespace App\View\Components\Avatar;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CampaignOptions extends Component
{
    /**
     * @psalm-suppress InvalidReturnStatement
     */
    public function render(): View
    {
        return view('components.avatar.campaign-options');
    }
}
