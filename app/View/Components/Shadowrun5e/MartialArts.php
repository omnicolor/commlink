<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\MartialArtsStyleArray;
use App\Models\Shadowrun5e\MartialArtsTechniqueArray;
use App\Models\Shadowrun5e\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class MartialArts extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;
    public MartialArtsStyleArray $styles;
    public MartialArtsTechniqueArray $techniques;

    /**
     * Create a new component instance.
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
    public function render(): View
    {
        /** @var View */
        $view = view('components.shadowrun5e.martial-arts');
        return $view;
    }
}
