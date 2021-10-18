<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\Commlink;
use App\Models\Shadowrun5E\Gear as GearModel;
use App\Models\Shadowrun5E\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

class Matrix extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's matrix devices.
     * @var Collection
     */
    public Collection $devices;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->devices = collect($character->getGear())
            ->filter(function (GearModel $item, int $key): bool {
                // Filter non-matrix devices out, they're shown in a different
                // section.
                return $item instanceof Commlink;
            });
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): view
    {
        return view('components.shadowrun5e.matrix');
    }
}
