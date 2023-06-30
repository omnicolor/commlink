<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\Shadowrun5e\SkillArray;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Knowledge extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;

    public SkillArray $knowledges;
    public SkillArray $languages;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->knowledges = $character->getKnowledgeSkills(onlyKnowledges: true);
        $this->languages = $character->getKnowledgeSkills(onlyLanguages: true);
    }

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.shadowrun5e.knowledge');
    }
}
