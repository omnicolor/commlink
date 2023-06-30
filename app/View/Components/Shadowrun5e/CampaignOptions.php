<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CampaignOptions extends Component
{
    /**
     * Collection of books.
     * @var array<string, string|bool>
     */
    public array $books;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $filename = config('app.data_path.shadowrun5e') . 'rulebooks.php';
        $this->books = require $filename;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View
    {
        return view('components.shadowrun5e.campaign-options');
    }
}
