<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\Shadowrun5e\QualityArray;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Qualities extends Component
{
    public bool $charGen;
    public QualityArray $qualities;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->qualities = $character->getQualities();
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render()
    {
        return view('components.shadowrun5e.qualities');
    }
}
