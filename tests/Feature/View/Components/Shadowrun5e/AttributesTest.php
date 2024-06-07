<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Attributes;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class AttributesTest extends TestCase
{
    /**
     * Test rendering a mundane character.
     */
    public function testMundaneCharacter(): void
    {
        self::component(Attributes::class, ['character' => new Character()])
            ->assertDontSee("Magic\n")
            ->assertDontSee("Resonance\n");
    }

    /**
     * Test rendering a new character.
     */
    public function testNewCharacter(): void
    {
        self::component(
            Attributes::class,
            ['character' => new PartialCharacter()]
        )
            ->assertDontSee("Magic\n")
            ->assertDontSee("Resonance\n");
    }

    /**
     * Test rendering a mage character.
     */
    public function testMage(): void
    {
        self::component(
            Attributes::class,
            [
                'character' => new Character([
                    'priorities' => [
                        'magic' => 'magician',
                    ],
                    'magic' => 6,
                ]),
            ]
        )
            ->assertSee('Magic')
            ->assertSee('magic-natural">6', false)
            ->assertDontSee("Resonance\n");
    }
}
