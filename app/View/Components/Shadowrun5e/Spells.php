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
     */
    public bool $charGen = false;
    public SpellArray $spells;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'Shadowrun5e\Spells';
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
        /**
         * @phpstan-ignore-next-line
         * @psalm-suppress DocblockTypeContradiction
         */
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
     */
    public function render(): View
    {
        return view('components.shadowrun5e.spells');
    }
}
