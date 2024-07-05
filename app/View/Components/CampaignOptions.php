<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Campaign;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use function sprintf;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class CampaignOptions extends Component
{
    public function __construct(public Campaign $campaign)
    {
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'CampaignOptions';
    }

    /**
     * @psalm-suppress InvalidReturnStatement
     */
    public function render(): View
    {
        $systemView = sprintf(
            '%s::components.campaign-metadata',
            $this->campaign->system
        );
        if (view()->exists($systemView)) {
            return view($systemView);
        }
        return view('components.campaign-options');
    }
}
