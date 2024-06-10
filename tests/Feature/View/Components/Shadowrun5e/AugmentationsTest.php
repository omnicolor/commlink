<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Augmentations;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class AugmentationsTest extends TestCase
{
    /**
     * Test rendering a lack of augmentations for an existing character.
     */
    public function testNoAugmentationsExistingCharacter(): void
    {
        self::component(Augmentations::class, ['character' => new Character()])
            ->assertDontSee('Trade humanity for an edge');
    }

    /**
     * Test rendering a lack of weapons for a new character.
     */
    public function testNoAugmentationsNewCharacter(): void
    {
        self::component(
            Augmentations::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('Trade humanity for an edge');
    }

    /**
     * Test rendering a character with a weapon.
     */
    public function testWithAugmentation(): void
    {
        self::component(
            Augmentations::class,
            [
                'character' => new Character([
                    'augmentations' => [
                        ['id' => 'damper'],
                    ],
                ]),
            ]
        )
            ->assertSee('Damper')
            ->assertDontSee('Trade humanity for an edge');
    }
}
