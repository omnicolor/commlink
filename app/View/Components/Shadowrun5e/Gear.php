<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\Commlink;
use App\Models\Shadowrun5e\Gear as GearModel;
use App\Models\Shadowrun5e\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
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
     * @var Collection
     */
    public Collection $gears;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->gears = collect($character->getGear())
            ->filter(function (GearModel $item, int $key): bool {
                // Filter matrix devices out, they're shown in a different
                // section.
                return !($item instanceof Commlink);
            });
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
