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
     */
    public bool $charGen;
    public IdentityArray $identities;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Identities';
        $this->identities = $character->getIdentities();
    }

    /**
     * Get the view that represents the component.
     * @psalm-suppress InvalidReturnStatement
     */
    public function render(): View
    {
        return view('components.shadowrun5e.identities');
    }
}
