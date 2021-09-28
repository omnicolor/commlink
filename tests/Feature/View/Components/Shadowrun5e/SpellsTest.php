<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\PartialCharacter;
use App\View\Components\Shadowrun5e\Spells;

/**
 * @small
 */
final class SpellsTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of spells for an existing character.
     * @test
     */
    public function testNoSpellsExistingCharacter(): void
    {
        $this->component(
            Spells::class,
            [
                'character' => new Character([
                    'priorities' => [
                        'magic' => 'magician',
                    ],
                ]),
            ]
        )
            ->assertDontSee('spells')
            ->assertDontSee('Character has no spells');
    }

    /**
     * Test rendering a new character without their priorities in order.
     * @test
     */
    public function testNoPrioritiesNewCharacter(): void
    {
        $this->component(
            Spells::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('Learn some')
            ->assertSee('Character has no spells.');
    }

    /**
     * Test rendering a new mundane character.
     * @test
     */
    public function testNoPrioritiesMundaneCharacter(): void
    {
        $this->component(
            Spells::class,
            [
                'character' => new PartialCharacter([
                    'priorities' => [
                        'magic' => null,
                    ],
                ]),
            ]
        )
            ->assertDontSee('Character has no spells.');
    }

    /**
     * Test rendering a new technomancer character.
     * @test
     */
    public function testNoPrioritiesTechnoCharacter(): void
    {
        $this->component(
            Spells::class,
            [
                'character' => new PartialCharacter([
                    'priorities' => [
                        'magic' => 'technomancer',
                    ],
                ]),
            ]
        )
            ->assertDontSee('Character has no spells.');
    }

    /**
     * Test rendering a lack of spells for a new magician character.
     * @test
     */
    public function testNoSpellsNewCharacter(): void
    {
        $this->component(
            Spells::class,
            [
                'character' => new PartialCharacter([
                    'priorities' => [
                        'magic' => 'magician',
                    ],
                ]),
            ]
        )
            ->assertSee('Learn some')
            ->assertSee('Character has no spells.');
    }

    /**
     * Test rendering a character with some spells.
     * @test
     */
    public function testWithSpell(): void
    {
        $this->component(
            Spells::class,
            [
                'character' => new Character([
                    'magics' => [
                        'spells' => [
                            'control-emotions',
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('Control Emotions')
            ->assertDontSee('Character has no spells.');
    }
}
