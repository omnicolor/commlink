<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\PartialCharacter;
use App\View\Components\Shadowrun5e\Weapons;

/**
 * @small
 */
final class WeaponsTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of weapons for an existing character.
     * @test
     */
    public function testNoWeaponsExistingCharacter(): void
    {
        $this->component(Weapons::class, ['character' => new Character()])
            ->assertDontSee('Purchase weapons on')
            ->assertSee('Character is unarmed.');
    }

    /**
     * Test rendering a lack of weapons for a new character.
     * @test
     */
    public function testNoWeaponsNewCharacter(): void
    {
        $this->component(
            Weapons::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('Purchase weapons on')
            ->assertSee('Character is unarmed.');
    }

    /**
     * Test rendering a character with a weapon.
     * @test
     */
    public function testWithWeapon(): void
    {
        $this->component(
            Weapons::class,
            [
                'character' => new Character([
                    'weapons' => [
                        ['id' => 'ak-98'],
                    ],
                ]),
            ]
        )
            ->assertSee('AK-98')
            ->assertDontSee('Character is unarmed.');
    }
}
