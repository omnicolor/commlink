<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\View\Components;

use Modules\Shadowrun5e\Models\Character;
use Modules\Shadowrun5e\Models\PartialCharacter;
use Modules\Shadowrun5e\View\Components\Matrix;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class MatrixTest extends TestCase
{
    /**
     * Test rendering a lack of matrix devices for an existing character.
     */
    public function testNoMatrixExistingCharacter(): void
    {
        $this->component(Matrix::class, ['character' => new Character()])
            ->assertDontSee('matrix')
            ->assertDontSee('Character has no matrix devices');
    }

    /**
     * Test rendering a lack of matrix devices for a new character.
     */
    public function testNoArmorNewCharacter(): void
    {
        $this->component(Matrix::class, ['character' => new PartialCharacter()])
            ->assertSee('matrix')
            ->assertSee('Character has no matrix devices');
    }

    /**
     * Test rendering a matrix device.
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
