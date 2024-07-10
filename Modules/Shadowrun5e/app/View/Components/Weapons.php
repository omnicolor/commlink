<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\WeaponArray;

/**
 * @psalm-suppress UnusedClass
 */
class Weapons extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;
    public WeaponArray $weapons;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Weapons';
        $this->weapons = $character->getWeapons();
    }

    public function render(): View
    {
        return view('shadowrun5e::components.weapons');
    }
}
