<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Character;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Nwidart\Modules\Facades\Module;
use Spatie\LaravelIgnition\Exceptions\ViewException;

use function route;
use function view;

class CharacterList extends Component
{
    /**
     * @param Collection<int, Character> $characters
     */
    public function __construct(public Collection $characters)
    {
        $systems = [
            'capers',
            'cyberpunkred',
            'expanse',
            'shadowrun5e',
            'star-trek-adventures',
            //'stillfleet',
            //'subversion',
        ];
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'CharacterList';
        foreach ($this->characters as $character) {
            $system = $character->system;
            if (null !== Module::find($system) && Module::isEnabled($system)) {
                // @phpstan-ignore-next-line
                try {
                    $character->link = route($system . '.character', $character);
                } catch (ViewException) {
                    // Ignore a system not being ready.
                }
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
