<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Attributes extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'Shadowrun5e\Attributes';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.shadowrun5e.attributes');
    }
}
