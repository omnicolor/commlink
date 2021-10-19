<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\MartialArtsStyleArray;
use App\Models\Shadowrun5E\MartialArtsTechniqueArray;
use App\Models\Shadowrun5E\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MartialArts extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's martial arts styles.
     * @var MartialArtsStyleArray
     */
    public MartialArtsStyleArray $styles;

    /**
     * Character's martial arts techniques.
     * @var MartialArtsTechniqueArray
     */
    public MartialArtsTechniqueArray $techniques;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->styles = $character->getMartialArtsStyles();
        $this->techniques = $character->getMartialArtsTechniques();
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): view
    {
        return view('components.shadowrun5e.martial-arts');
    }
}
