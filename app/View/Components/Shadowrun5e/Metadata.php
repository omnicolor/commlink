<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Metadata extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
    }

    /**
     * Get the view / contents that represent the component.
     * @return View
     */
    public function render(): View
    {
        /** @var View */
        $view = view('components.shadowrun5e.metadata');
        return $view;
    }
}
