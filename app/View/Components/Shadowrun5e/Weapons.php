<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\Shadowrun5e\WeaponArray;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Weapons extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's weapons.
     * @var WeaponArray
     */
    public WeaponArray $weapons;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->weapons = $character->getWeapons();
    }

    /**
     * Get the view / contents that represent the component.
     * @return View
     */
    public function render(): View
    {
        /** @var View */
        $view = view('components.shadowrun5e.weapons');
        return $view;
    }
}
