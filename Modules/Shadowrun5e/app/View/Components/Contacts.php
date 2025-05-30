<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\ContactArray;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Override;

use function view;

class Contacts extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;
    public ContactArray $contacts;

    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Contacts';
        $this->contacts = $character->getContacts();
    }

    #[Override]
    public function render(): View
    {
        return view('shadowrun5e::components.contacts');
    }
}
