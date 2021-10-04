<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\GearArray;
use App\Models\Shadowrun5E\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Gear extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's gear.
     * @var GearArray
     */
    public GearArray $gears;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->gears = $character->getGear();
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): view
    {
        return view('components.shadowrun5e.gear');
    }
}
