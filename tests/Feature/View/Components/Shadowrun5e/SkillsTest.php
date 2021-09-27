<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\PartialCharacter;
use App\View\Components\Shadowrun5e\Skills;

/**
 * @small
 */
final class SkillsTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of skills with an existing character.
     * @test
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
     * @test
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
     * @test
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
