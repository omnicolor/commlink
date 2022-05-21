<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Vehicles;

/**
 * @small
 */
final class VehiclesTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of vehicles for an existing character.
     * @test
     */
    public function testNoVehiclesExistingCharacter(): void
    {
        $this->component(Vehicles::class, ['character' => new Character()])
            ->assertDontSee('vehicles')
            ->assertDontSee('Character has no vehicles.');
    }

    /**
     * Test a lack of vehicles for a new character.
     * @test
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
     * @test
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
