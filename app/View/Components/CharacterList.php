<?php

declare(strict_types=1);

namespace App\View\Components;

use App\Models\Character;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;
use Nwidart\Modules\Facades\Module;
use Spatie\LaravelIgnition\Exceptions\ViewException;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

use function route;
use function view;

/**
 * @psalm-suppress UnusedClass
 */
class CharacterList extends Component
{
    /**
     * @param Collection<int, Character> $characters
     */
    public function __construct(public Collection $characters)
    {
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'CharacterList';
        foreach ($this->characters as $character) {
            /**
             * @psalm-suppress UndefinedMagicPropertyAssignment
             * @phpstan-ignore property.notFound
             */
            $character->link = false;
            $system = $character->system;
            if (null !== Module::find($system) && Module::isEnabled($system)) {
                try {
                    /** @psalm-suppress UndefinedMagicPropertyAssignment */
                    $character->link = route($system . '.character', $character);
                    continue;
                } catch (RouteNotFoundException | ViewException) { // @codeCoverageIgnore
                    // Ignore a system not being ready or disabled.
                }
            }
        }
    }

    public function render(): View
    {
        return view('components.character-list');
    }
}
