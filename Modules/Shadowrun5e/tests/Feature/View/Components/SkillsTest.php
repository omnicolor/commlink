<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\View\Components;

use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\View\Components\Skills;
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
        $view = $this->component(
            Skills::class,
            ['character' => new Character()]
        )
            ->assertDontSee('No skill groups purchased.')
            ->assertDontSee('No skills purchased.');
        self::assertInstanceOf(Skills::class, $view->component);
        self::assertFalse($view->component->charGen);
    }

    /**
     * Test rendering skills with a new character.
     */
    public function testNoSkillsNewCharacter(): void
    {
        $view = $this->component(
            Skills::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('No skill groups purchased.')
            ->assertSee('No skills purchased.');
        self::assertInstanceOf(Skills::class, $view->component);
        self::assertTrue($view->component->charGen);
    }

    /**
     * Test rendering a skill.
     */
    public function testRenderSkill(): void
    {
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
        self::assertInstanceOf(Skills::class, $view->component);
        self::assertTrue($view->component->charGen);
    }
}
