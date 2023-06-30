<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\AugmentationArray;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Augmentations extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;
    public AugmentationArray $augmentations;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->augmentations = $character->getAugmentations();
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.shadowrun5e.augmentations');
    }
}
