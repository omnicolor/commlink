<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\VehicleModification as Modification;

/**
 * @psalm-suppress UnusedClass
 */
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
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('shadowrun5e::components.vehicle-modification');
    }
}
