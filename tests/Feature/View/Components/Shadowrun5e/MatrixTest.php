<?php

declare(strict_types=1);

namespace Tests\Feature\View\Components\Shadowrun5e;

use App\Models\Shadowrun5e\Character;
use App\Models\Shadowrun5e\PartialCharacter;
use App\View\Components\Shadowrun5e\Matrix;

/**
 * @small
 */
final class MatrixTest extends \Tests\TestCase
{
    /**
     * Test rendering a lack of matrix devices for an existing character.
     * @test
     */
    public function testNoMatrixExistingCharacter(): void
    {
        $this->component(Matrix::class, ['character' => new Character()])
            ->assertDontSee('matrix')
            ->assertDontSee('Character has no matrix devices');
    }

    /**
     * Test rendering a lack of matrix devices for a new character.
     * @test
     */
    public function testNoArmorNewCharacter(): void
    {
        $this->component(Matrix::class, ['character' => new PartialCharacter()])
            ->assertSee('matrix')
            ->assertSee('Character has no matrix devices');
    }

    /**
     * Test rendering a matrix device.
     * @test
     */
    public function testMatrixDevice(): void
    {
        $this->component(
            Matrix::class,
            [
                'character' => new Character([
                    'gear' => [
                        [
                            'id' => 'commlink-sony-angel',
                            'quantity' => true,
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('Sony Angel')
            ->assertSee('matrix')
            ->assertDontSee('Character has no matrix devices');
    }

    /**
     * Test that other gear doesn't show up in the matrix area.
     * @test
     */
    public function testOnlyNonMatrixDevices(): void
    {
        $this->component(
            Matrix::class,
            [
                'character' => new PartialCharacter([
                    'gear' => [
                        [
                            'id' => 'ear-buds-1',
                            'quantity' => true,
                        ],
                    ],
                ]),
            ]
        )
            ->assertSee('matrix')
            ->assertSee('Character has no matrix devices');
    }
}
