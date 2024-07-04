<?php

declare(strict_types=1);

namespace App\View\Components\Cyberpunkred;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * @psalm-suppress UnusedClass
 */
class CampaignOptions extends Component
{
    /**
     * @psalm-suppress InvalidReturnStatement
     */
    public function render(): View
    {
        return view('components.cyberpunkred.campaign-options');
    }
}
