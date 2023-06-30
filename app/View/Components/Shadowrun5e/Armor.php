<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\ArmorArray;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

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
        $this->charGen = $character instanceof PartialCharacter;
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.shadowrun5e.armor');
    }
}
