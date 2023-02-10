<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\Shadowrun5e\VehicleArray;
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
        /** @var View */
        $view = view('components.shadowrun5e.vehicles');
        return $view;
    }
}
