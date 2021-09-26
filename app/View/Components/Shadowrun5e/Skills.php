<?php

declare(strict_types=1);

namespace App\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\SkillArray;
use App\Models\Shadowrun5E\SkillGroup;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class Skills extends Component
{
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
     * @param bool $charGen
     */
    public function __construct(public Character $character, public bool $charGen = false)
    {
        $this->skillGroups = $character->getSkillGroups();
        $this->skills = $character->getSkills();
    }

    /**
     * Get the view that represent the component.
     * @return View
     */
    public function render(): View
    {
        return view('components.shadowrun5e.skills');
    }
}
