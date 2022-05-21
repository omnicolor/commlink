<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\AdeptPowerArray;
use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Powers extends Component
{
    /**
     * Whether the character is still being built.
     * @var bool
     */
    public bool $charGen;

    /**
     * Whether the character is an adept in the first place.
     * @var bool
     */
    public bool $isAdept;

    /**
     * Character's powers.
     * @var AdeptPowerArray
     */
    public AdeptPowerArray $powers;

    /**
     * Type of adept: adept, mystic adept, or an empty string.
     * @var string
     */
    public string $type;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->isAdept = $this->isAdept();
        $this->powers = $character->getAdeptPowers();
        $this->type = $this->getAdeptType();
    }

    /**
     * Return the adept's type.
     * @return string
     */
    protected function getAdeptType(): string
    {
        if (!$this->isAdept()) {
            return '';
        }
        // @phpstan-ignore-next-line
        if ('mystic' === $this->character->priorities['magic']) {
            return 'mystic adept';
        }
        return 'adept';
    }

    /**
     * Determine whether the character can have adept powers.
     * @return bool
     */
    protected function isAdept(): bool
    {
        $adeptTypes = ['adept', 'mystic'];
        return isset($this->character->priorities['magic'])
            && in_array($this->character->priorities['magic'], $adeptTypes, true);
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.shadowrun5e.powers');
    }
}
