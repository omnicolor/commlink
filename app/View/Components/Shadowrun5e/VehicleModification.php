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
    }

    /**
     * Get the view that represents the component.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('components.shadowrun5e.vehicle-modification');
    }
}
