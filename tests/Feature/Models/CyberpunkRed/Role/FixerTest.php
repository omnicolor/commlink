<?php

declare(strict_types=1);

namespace Tests\Feature\Models\CyberpunkRed\Role;

use App\Models\CyberpunkRed\Role\Fixer;

/**
 * Tests for the Fixer role.
 * @group cyberpunkred
 * @group models
 * @small
 */
final class FixerTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the toString method.
     * @test
     */
    public function testToString(): void
    {
        $fixer = new Fixer([
            'rank' => 4,
            'type' => Fixer::TYPE_BROKER_DEALS,
        ]);
        self::assertSame('Fixer', (string)$fixer);
    }

    /**
     * Test that all of the different fixer types return information for
     * getType().
     * @test
     */
    public function testGetType(): void
    {
        $types = [
            Fixer::TYPE_BROKER_DEALS,
            Fixer::TYPE_PROCURE_ATYPICAL,
            Fixer::TYPE_BROKER_SERVICES,
            Fixer::TYPE_SUPPLY_REGULAR,
            Fixer::TYPE_PROCURE_ILLEGAL,
            Fixer::TYPE_SUPPLY_RESOURCES,
            Fixer::TYPE_OPERATE_NIGHT_MARKETS,
            Fixer::TYPE_BROKER_CONTRACTS,
            Fixer::TYPE_BROKER_FENCE,
            Fixer::TYPE_EXCLUSIVE_AGENT,
        ];

        foreach ($types as $type) {
            $fixer = new Fixer([
                'rank' => 4,
                'type' => $type,
            ]);
            self::assertIsString($fixer->getType());
        }
    }

    /**
     * Test setting the fixer's type to an invalid value throws an exception.
     * @test
     */
    public function testGetTypeInvalid(): void
    {
        $fixer = new Fixer(['rank' => 4, 'type' => 42]);
        self::expectException(\OutOfBoundsException::class);
        $fixer->getType();
    }
}
