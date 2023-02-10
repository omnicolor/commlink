<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\IdentityArray;
use App\Models\Shadowrun5e\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Identities extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;
    public IdentityArray $identities;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->identities = $character->getIdentities();
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): View
    {
        /** @var View */
        $view = view('components.shadowrun5e.identities');
        return $view;
    }
}
