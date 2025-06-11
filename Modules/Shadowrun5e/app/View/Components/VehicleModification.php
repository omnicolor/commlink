<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\VehicleModification as Modification;
use Override;

use function view;

class VehicleModification extends Component
{
    public function __construct(public Modification $mod)
    {
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'Shadowrun5e\VehicleModification';
    }

    #[Override]
    public function render(): View
    {
        return view('shadowrun5e::components.vehicle-modification');
    }
}
