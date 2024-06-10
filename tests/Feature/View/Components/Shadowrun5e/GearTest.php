<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Gear;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class GearTest extends TestCase
{
    /**
     * Test rendering a lack of gear for an existing character.
     */
    public function testNoGearExistingCharacter(): void
    {
        $this->component(Gear::class, ['character' => new Character()])
            ->assertDontSee('Buy some stuff')
            ->assertSee('No gear purchased.');
    }

    /**
     * Test rendering a lack of gear for a new character.
     */
    public function testNoArmorNewCharacter(): void
    {
        $this->component(Gear::class, ['character' => new PartialCharacter()])
            ->assertSee('Buy some stuff')
            ->assertSee('No gear purchased.');
    }

    /**
     * Test rendering some gear.
     */
    public function testGear(): void
    {
        $this->component(
            Gear::class,
            [
                'character' => new Character([
                    'gear' => [
                        [
                            'id' => 'ear-buds-1',
                            'quantity' => true,
                            'modifications' => [
                                'biomonitor',
                            ],
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('Ear Buds')
            ->assertSee('Biomonitor')
            ->assertDontSee('No gear purchased.');
    }

    /**
     * Test that matrix devices aren't considered gear for the purposes of
     * appearing in the gear section.
     */
    public function testOnlyMatrixDevices(): void
    {
        $this->component(
            Gear::class,
            [
                'character' => new PartialCharacter([
                    'gear' => [
                        [
                            'id' => 'commlink-sony-angel',
                            'quantity' => true,
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('Buy some stuff')
            ->assertSee('No gear purchased.');
    }
}
