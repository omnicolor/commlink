<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\VehicleModification as Modification;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class VehicleModification extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Modification $mod)
    {
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'Shadowrun5e\VehicleModification';
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.shadowrun5e.vehicle-modification');
    }
}
