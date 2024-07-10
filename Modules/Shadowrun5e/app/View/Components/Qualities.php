<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\QualityArray;

/**
 * @psalm-suppress UnusedClass
 */
class Qualities extends Component
{
    public bool $charGen;
    public QualityArray $qualities;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Qualities';
        $this->qualities = $character->getQualities();
    }

    public function render(): View
    {
        return view('shadowrun5e::components.qualities');
    }
}
