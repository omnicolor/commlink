<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\View\Component;

class CharacterList extends Component
{
    public function __construct(public Collection $characters)
    {
    }

    public function render(): View
    {
        return view('components.character-list');
    }
}
