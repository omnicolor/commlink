<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\AugmentationArray;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;

/**
 * @psalm-suppress UnusedClass
 */
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
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Augmentations';
        $this->augmentations = $character->getAugmentations();
    }

    /**
     * Get the view that represents the component.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('shadowrun5e::components.augmentations');
    }
}
