<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5E\Character;
use App\Models\Shadowrun5E\PartialCharacter;
use App\View\Components\Shadowrun5e\Augmentations;

/**
 * @small
 */
final class AugmentationsTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of augmentations for an existing character.
     * @test
     */
    public function testNoAugmentationsExistingCharacter(): void
    {
        $this->component(Augmentations::class, ['character' => new Character()])
            ->assertDontSee('Trade humanity for an edge');
    }

    /**
     * Test rendering a lack of weapons for a new character.
     * @test
     */
    public function testNoAugmentationsNewCharacter(): void
    {
        $this->component(
            Augmentations::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('Trade humanity for an edge');
    }

    /**
     * Test rendering a character with a weapon.
     * @test
     */
    public function testWithAugmentation(): void
    {
        $this->component(
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
