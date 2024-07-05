<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;

/**
 * @psalm-suppress UnusedClass
 */
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
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('shadowrun5e::components.attributes');
    }
}
