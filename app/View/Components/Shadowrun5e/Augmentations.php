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
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's augmentations.
     * @var AugmentationArray
     */
    public AugmentationArray $augmentations;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->augmentations = $character->getAugmentations();
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.shadowrun5e.augmentations');
    }
}
