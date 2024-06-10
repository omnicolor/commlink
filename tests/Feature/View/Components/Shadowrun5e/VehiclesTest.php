<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Vehicles;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class VehiclesTest extends TestCase
{
    /**
     * Test rendering a lack of vehicles for an existing character.
     */
    public function testNoVehiclesExistingCharacter(): void
    {
        $this->component(Vehicles::class, ['character' => new Character()])
            ->assertDontSee('vehicles')
            ->assertDontSee('Character has no vehicles.');
    }

    /**
     * Test a lack of vehicles for a new character.
     */
    public function testNoVehiclesNewCharacter(): void
    {
        $this->component(
            Vehicles::class,
            ['character' => new PartialCharacter()]
        )
            ->assertSee('vehicles')
            ->assertSee('Character has no vehicles.');
    }

    /**
     * Test rendering a vehicle.
     */
    public function testVehicles(): void
    {
        $this->component(
            Vehicles::class,
            [
                'character' => new Character([
                    'vehicles' => [
                        [
                            'id' => 'mct-fly-spy',
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('vehicles')
            ->assertDontSee('Character has no vehicles.');
    }
}
