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
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's non-language knowledge skills.
     * @var SkillArray
     */
    public SkillArray $knowledges;

    /**
     * Character's languages.
     * @var SkillArray
     */
    public SkillArray $languages;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->knowledges = $character->getKnowledgeSkills(onlyKnowledges: true);
        $this->languages = $character->getKnowledgeSkills(onlyLanguages: true);
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): view
    {
        return view('components.shadowrun5e.knowledge');
    }
}
