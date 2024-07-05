<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\SkillArray;

/**
 * @psalm-suppress UnusedClass
 */
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
        $this->knowledges = $character->getKnowledgeSkills(onlyKnowledges: true);
        $this->languages = $character->getKnowledgeSkills(onlyLanguages: true);
    }

    /**
     * Get the view that represents the component.
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidReturnType
     */
    public function render(): View
    {
        return view('shadowrun5e::components.knowledge');
    }
}
