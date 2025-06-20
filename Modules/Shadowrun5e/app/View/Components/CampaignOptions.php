<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Override;

use function config;
use function view;

class CampaignOptions extends Component
{
    /**
     * Collection of books.
     * @var array<string, string|bool>
     */
    public array $books;

    public function __construct()
    {
        $this->attributes = $this->newAttributeBag();
        $filename = config('shadowrun5e.data_path') . 'rulebooks.php';
        $this->books = require $filename;
        $this->componentName = 'Shadowrun5e\CampaignOptions';
    }

    #[Override]
    public function render(): View
    {
        return view('shadowrun5e::components.campaign-options');
    }
}
