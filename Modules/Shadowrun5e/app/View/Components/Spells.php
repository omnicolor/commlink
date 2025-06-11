<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\SpellArray;
use Override;

use function view;

class Spells extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen = false;
    public SpellArray $spells;

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
        if (!isset($character->priorities['magic'])) {
            return;
        }
        if ('technomancer' === $character->priorities['magic']) {
            return;
        }
        $this->charGen = true;
    }

    #[Override]
    public function render(): View
    {
        return view('shadowrun5e::components.spells');
    }
}
