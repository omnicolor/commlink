<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Perk;
use RuntimeException;

/**
 * Tests for Capers perks.
 * @group capers
 * @small
 */
final class PerkTest extends \Tests\TestCase
{
    /**
     * Test trying to create a perk that doesn't exist.
     * @test
     */
    public function testInvalidPerk(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Perks ID "invalid" is invalid');
        new Perk('invalid', []);
    }

    /**
     * Test creating a perk and casting it to a string.
     * @test
     */
    public function testToString(): void
    {
        $perk = new Perk('fleet-of-foot', []);
        self::assertSame('Fleet of Foot', (string)$perk);
        self::assertSame('fleet-of-foot', $perk->id);
        self::assertSame(
            'Your character’s foot Speed increases to 40’.',
            $perk->description
        );
        self::assertNull($perk->skillId);
    }

    /**
     * Test the specialty skill perk, which populates the skillId property.
     * @test
     */
    public function testSpecialtyString(): void
    {
        $perk = new Perk('specialty-skill', ['skill' => 'acrobatics']);
        self::assertSame('acrobatics', $perk->skillId);
    }
}
