<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Attributes;
use Tests\TestCase;

/**
 * @small
 */
final class AttributesTest extends TestCase
{
    /**
     * Test rendering a mundane character.
     * @test
     */
    public function testMundaneCharacter(): void
    {
        $this->component(Attributes::class, ['character' => new Character()])
            ->assertDontSee("Magic\n")
            ->assertDontSee("Resonance\n");
    }

    /**
     * Test rendering a new character.
     * @test
     */
    public function testNewCharacter(): void
    {
        $this->component(
            Attributes::class,
            ['character' => new PartialCharacter()]
        )
            ->assertDontSee("Magic\n")
            ->assertDontSee("Resonance\n");
    }

    /**
     * Test rendering a mage character.
     * @test
     */
    public function testMage(): void
    {
        $this->component(
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
