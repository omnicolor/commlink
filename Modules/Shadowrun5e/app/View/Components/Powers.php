<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\AdeptPowerArray;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Override;

use function assert;
use function in_array;
use function view;

class Powers extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;

    public bool $isAdept;
    public AdeptPowerArray $powers;

    /**
     * Type of adept: adept, mystic adept, or an empty string.
     */
    public string $type;

    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Powers';
        $this->isAdept = $this->isAdept();
        $this->powers = $character->getAdeptPowers();
        $this->type = $this->getAdeptType();
    }

    /**
     * Return the adept's type.
     */
    protected function getAdeptType(): string
    {
        if (!$this->isAdept()) {
            return '';
        }
        assert(null !== $this->character->priorities);
        if ('mystic' === $this->character->priorities['magic']) {
            return 'mystic adept';
        }
        return 'adept';
    }

    /**
     * Determine whether the character can have adept powers.
     */
    protected function isAdept(): bool
    {
        $adeptTypes = ['adept', 'mystic'];
        return isset($this->character->priorities['magic'])
            && in_array($this->character->priorities['magic'], $adeptTypes, true);
    }

    #[Override]
    public function render(): View
    {
        return view('shadowrun5e::components.powers');
    }
}
