<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Powers;
use Tests\TestCase;

/**
 * @small
 */
final class PowersTest extends TestCase
{
    /**
     * Test rendering a lack of powers for an existing character.
     * @test
     */
    public function testNoPowersExistingCharacter(): void
    {
        $this->component(
            Powers::class,
            [
                'character' => new Character([
                    'priorities' => [
                        'magic' => 'adept',
                    ],
                ]),
            ]
        )
            ->assertDontSee('powers')
            ->assertDontSee('Your adept has not picked up any powers.');
    }

    /**
     * Test rendering a lack of powers for a mundane new character.
     * @test
     */
    public function testNoPowersNewMundaneCharacter(): void
    {
        $this->component(
            Powers::class,
            [
                'character' => new PartialCharacter([
                    'priorities' => [
                        'magic' => null,
                    ],
                ]),
            ]
        )
            ->assertDontSee('powers')
            ->assertDontSee('has not picked up any powers.');
    }

    /**
     * Test rendering a lack of powers for a new adept.
     * @test
     */
    public function testNoPowersNewAdept(): void
    {
        $this->component(
            Powers::class,
            [
                'character' => new PartialCharacter([
                    'priorities' => [
                        'magic' => 'adept',
                    ],
                ]),
            ]
        )
            ->assertSee('powers')
            ->assertSee('Your adept has not picked up any powers.')
            ->assertSee('bg-danger')
            ->assertDontSee('bg-warning');
    }

    /**
     * Test rendering a lack of powers for a new mystic adept.
     * @test
     */
    public function testNoPowersNewMysticAdept(): void
    {
        $this->component(
            Powers::class,
            [
                'character' => new PartialCharacter([
                    'priorities' => [
                        'magic' => 'mystic',
                    ],
                ]),
            ]
        )
            ->assertSee('powers')
            ->assertSee('Your mystic adept has not picked up any powers.')
            ->assertSee('bg-warning')
            ->assertDontSee('bg-danger');
    }

    /**
     * Test rendering some powers.
     * @test
     */
    public function testPowers(): void
    {
        $this->component(
            Powers::class,
            [
                'character' => new Character([
                    'magics' => [
                        'powers' => [
                            'combat-sense-2',
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('powers')
            ->assertSee('Combat Sense')
            ->assertDontSee('has not picked up any powers');
    }
}
