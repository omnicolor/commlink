<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Armor;

/**
 * @small
 */
final class ArmorTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of armor for an existing character.
     * @test
     */
    public function testNoArmorExistingCharacter(): void
    {
        $this->component(Armor::class, ['character' => new Character()])
            ->assertDontSee('armor: ', false)
            ->assertDontSee('Character has no armor');
    }

    /**
     * Test rendering a lack of armor for a new character.
     * @test
     */
    public function testNoArmorNewCharacter(): void
    {
        $this->component(Armor::class, ['character' => new PartialCharacter()])
            ->assertSee('armor: <span id="armor-value">0', false)
            ->assertSee('Character has no armor');
    }

    /**
     * Test rendering some armor.
     * @test
     */
    public function testArmor(): void
    {
        $this->component(
            Armor::class,
            [
                'character' => new Character([
                    'armor' => [
                        ['id' => 'armor-jacket', 'active' => true],
                    ],
                ]),
            ]
        )
            ->assertSee('armor: <span id="armor-value">12', false)
            ->assertSee('Armor Jacket')
            ->assertDontSee('Character has no armor');
    }
}
