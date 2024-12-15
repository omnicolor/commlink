<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\View\Components;

use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\Models\SkillArray;
use Modules\Shadowrun5e\Models\SkillGroup;

class Skills extends Component
{
    /**
     * Whether the character is still being built.
     */
    public bool $charGen;

    /**
     * Character's skill groups.
     * @var array<int, SkillGroup>
     */
    public array $skillGroups;

    public SkillArray $skills;

    /**
     * Create a new component instance.
     */
    public function __construct(public Character $character)
    {
        $this->attributes = $this->newAttributeBag();
        $this->charGen = $character instanceof PartialCharacter;
        $this->componentName = 'Shadowrun5e\Skills';
        $this->skillGroups = $character->getSkillGroups();
        $this->skills = $character->getSkills();
    }

    public function render(): View
    {
        return view('shadowrun5e::components.skills');
    }
}
