<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\Shadowrun5e\SpellArray;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Spells extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen = false;

    /**
     * Character's spells.
     * @var SpellArray
     */
    public SpellArray $spells;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->spells = $character->getSpells();
        if (!($character instanceof PartialCharacter)) {
            return;
        }
        if (!isset($character->priorities)) {
            // Character hasn't set their priorities yet, consider them
            // potentially magically active.
            $this->charGen = true;
            return;
        }
        // @phpstan-ignore-next-line
        if (!isset($character->priorities['magic']) || null === $character->priorities['magic']) {
            return;
        }
        if ('technomancer' === $character->priorities['magic']) {
            return;
        }
        $this->charGen = true;
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): view
    {
        return view('components.shadowrun5e.spells');
    }
}
