<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Weapons;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class WeaponsTest extends TestCase
{
    #[TestDox('Test rendering a lack of weapons for an existing character')]
    public function testNoWeaponsExistingCharacter(): void
    {
        self::component(Weapons::class, ['character' => new Character()])
            ->assertDontSee('Purchase weapons on')
            ->assertSee('Character is unarmed.');
    }

    #[TestDox('Test rendering a lack of weapons for a new character')]
    public function testNoWeaponsNewCharacter(): void
    {
        self::component(
            Weapons::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('Purchase weapons on')
            ->assertSee('Character is unarmed.');
    }

    #[TestDox('Test rendering a character with a weapon')]
    public function testWithWeapon(): void
    {
        self::component(
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
