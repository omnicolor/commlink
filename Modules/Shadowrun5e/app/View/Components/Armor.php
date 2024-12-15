<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\ArmorArray;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;

class Armor extends Component
{
    public ArmorArray $armors;

    /**
     * Whether the character is still being built.
     */
    public bool $charGen;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->armors = $character->getArmor();
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Armor';
    }

    public function render(): View
    {
        return view('shadowrun5e::components.armor');
    }
}
