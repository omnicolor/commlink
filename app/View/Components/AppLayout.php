<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     * @codeCoverageIgnore
     * @return \Illuminate\View\View
     */
    public function render(): \Illuminate\View\View
    {
        // @phpstan-ignore-next-line
        return view('layouts.app');
    }
}
