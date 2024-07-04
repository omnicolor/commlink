<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\View\Components;

use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\View\Components\Powers;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class PowersTest extends TestCase
{
    /**
     * Test rendering a lack of powers for an existing character.
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
