<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\SkillArray;

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
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Knowledge';
        $this->knowledges = $character->getKnowledgeSkills(only_knowledges: true);
        $this->languages = $character->getKnowledgeSkills(only_languages: true);
    }

    public function render(): View
    {
        return view('shadowrun5e::components.knowledge');
    }
}
