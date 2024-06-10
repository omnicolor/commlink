<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Skills;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SkillsTest extends TestCase
{
    /**
     * Test rendering a lack of skills with an existing character.
     */
    public function testNoSkillsExistingCharacter(): void
    {
        /** @var Skills */
        $view = $this->component(
            Skills::class,
            ['character' => new Character()]
        )
            ->assertDontSee('No skill groups purchased.')
            ->assertDontSee('No skills purchased.');
        self::assertFalse($view->charGen);
    }

    /**
     * Test rendering skills with a new character.
     */
    public function testNoSkillsNewCharacter(): void
    {
        /** @var Skills */
        $view = $this->component(
            Skills::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('No skill groups purchased.')
            ->assertSee('No skills purchased.');
        self::assertTrue($view->charGen);
    }

    /**
     * Test rendering a skill.
     */
    public function testRenderSkill(): void
    {
        /** @var Skills */
        $view = $this->component(
            Skills::class,
            [
                'character' => new PartialCharacter([
                    'skills' => [
                        ['id' => 'pistols', 'level' => 3],
                    ],
                ]),
            ]
        )
            ->assertSee('No skill groups purchased.')
            ->assertDontSee('No skills purchased.');
        self::assertTrue($view->charGen);
    }
}
