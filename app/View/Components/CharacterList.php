<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Character;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Nwidart\Modules\Facades\Module;

class CharacterList extends Component
{
    /**
     * @param Collection<int, Character> $characters
     */
    public function __construct(public Collection $characters)
    {
        $systems = [
            'avatar',
            'blistercritters',
            'capers',
            'cyberpunkred',
            'expanse',
            'shadowrun5e',
            'star-trek-adventures',
            //'stillfleet',
            //'subversion',
            'transformers',
        ];
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'CharacterList';
        foreach ($this->characters as $character) {
            $system = $character->system;
            if (null !== Module::find($system) && Module::isEnabled($system)) {
                // @phpstan-ignore-next-line
                $character->link = route($system . '.character', $character);
                continue;
            }
            if (in_array($system, $systems, true)) {
                // @phpstan-ignore-next-line
                $character->link = route($system . '.character', $character);
                continue;
            }
            // @phpstan-ignore-next-line
            $character->link = false;
        }
    }

    /**
     * @psalm-suppress InvalidReturnStatement
     */
    public function render(): View
    {
        return view('components.character-list');
    }
}
