<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

/**
 * @psalm-suppress UnusedClass
 */
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
        $this->attributes = $this->newAttributeBag();
        $filename = config('app.data_path.shadowrun5e') . 'rulebooks.php';
        /** @psalm-suppress UnresolvableInclude */
        $this->books = require $filename;
        $this->componentName = 'Shadowrun5e\CampaignOptions';
    }

    /**
     * Get the view / contents that represent the component.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('shadowrun5e::components.campaign-options');
    }
}