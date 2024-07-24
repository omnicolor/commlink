<?php

declare(strict_types=1);

namespace App\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * @psalm-suppress UnusedClass
 */
class AppLayout extends Component
{
    /**
     * Get the view / contents that represents the component.
     * @codeCoverageIgnore
     */
    public function render(): View
    {
        return view('layouts.app');
    }
}
