<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Spells;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SpellsTest extends TestCase
{
    /**
     * Test rendering a lack of spells for an existing character.
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
