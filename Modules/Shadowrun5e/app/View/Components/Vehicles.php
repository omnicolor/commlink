<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\VehicleArray;
use Override;

use function view;

class Vehicles extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;
    public VehicleArray $vehicles;

    public function __construct(Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Vehicles';
        $this->vehicles = $character->getVehicles();
    }

    #[Override]
    public function render(): View
    {
        return view('shadowrun5e::components.vehicles');
    }
}
