<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\AdeptPowerArray;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;

/**
 * @psalm-suppress UnusedClass
 */
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
     * @var string
     */
    public string $type;

    /**
     * Create a new component instance.
     */
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
        /**
         * @psalm-suppress PossiblyNullArrayAccess
         * @phpstan-ignore-next-line
         */
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

    /**
     * Get the view that represents the component.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('shadowrun5e::components.powers');
    }
}
