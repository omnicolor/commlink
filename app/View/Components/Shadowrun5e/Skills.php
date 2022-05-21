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
     * @var bool
     */
    public bool $charGen;

    /**
     * Character's skill groups.
     * @var array<int, SkillGroup>
     */
    public array $skillGroups;

    /**
     * Character's individual skills.
     * @var SkillArray
     */
    public SkillArray $skills;

    /**
     * Create a new component instance.
     * @param Character $character
     */
    public function __construct(public Character $character)
    {
        $this->charGen = $character instanceof PartialCharacter;
        $this->skillGroups = $character->getSkillGroups();
        $this->skills = $character->getSkills();
    }

    /**
     * Get the view that represents the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.shadowrun5e.skills');
    }
}
