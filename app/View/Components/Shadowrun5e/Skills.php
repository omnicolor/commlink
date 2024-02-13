<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\Models\Shadowrun5e\SkillArray;
use App\Models\Shadowrun5e\SkillGroup;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

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

    /**
     * Get the view that represents the component.
     */
    public function render(): View
    {
        return view('components.shadowrun5e.skills');
    }
}
