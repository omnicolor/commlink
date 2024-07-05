<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\Commlink;
use Modules\Shadowrun5e\Models\Gear as GearModel;
use Modules\Shadowrun5e\Models\PartialCharacter;

/**
 * @psalm-suppress UnusedClass
 */
class Matrix extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;
    public Collection $devices;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Matrix';
        $this->devices = collect($character->getGear())
            ->filter(function (GearModel $item): bool {
                // Filter non-matrix devices out, they're shown in a different
                // section.
                return $item instanceof Commlink;
            });
    }

    /**
     * Get the view that represents the component.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('shadowrun5e::components.matrix');
    }
}
