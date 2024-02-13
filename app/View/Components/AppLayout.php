<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     * @codeCoverageIgnore
     * @return View
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
