<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Override;

use function view;

class Attributes extends Component
{
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->componentName = 'Shadowrun5e\Attributes';
    }

    #[Override]
    public function render(): View
    {
        return view('shadowrun5e::components.attributes');
    }
}
