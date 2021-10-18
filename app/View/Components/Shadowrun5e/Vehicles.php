<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\PartialCharacter;
use App\Models\Shadowrun5E\VehicleArray;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Vehicles extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's vehicles.
     * @var VehicleArray
     */
    public VehicleArray $vehicles;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->vehicles = $character->getVehicles();
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.shadowrun5e.vehicles');
    }
}
