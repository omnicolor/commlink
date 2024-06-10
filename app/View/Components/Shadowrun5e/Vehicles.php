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
     */
    public bool $charGen;
    public VehicleArray $vehicles;

    /**
     * Create a new component instance.
     */
    public function __construct(Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Vehicles';
        $this->vehicles = $character->getVehicles();
    }

    /**
     * Get the view that represents the component.
     * @psalm-suppress InvalidReturnStatement
     */
    public function render(): View
    {
        return view('components.shadowrun5e.vehicles');
    }
}
