<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class CharacterList extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct(public Collection $characters)
    {
    }

    /**
     * Get the view / contents that represent the component.
     * @return View
     */
    public function render(): View
    {
        /** @var View */
        $view = view('components.character-list');
        return $view;
    }
}
