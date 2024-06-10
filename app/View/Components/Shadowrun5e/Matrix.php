<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\Commlink;
use App\Models\Shadowrun5e\Gear as GearModel;
use App\Models\Shadowrun5e\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use Illuminate\View\Component;

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
     */
    public function render(): View
    {
        return view('components.shadowrun5e.matrix');
    }
}
