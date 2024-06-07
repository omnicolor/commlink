<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\CritterWeakness;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for critter weaknesses.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class CritterWeaknessTest extends TestCase
{
    /**
     * Test trying to load an invalid weakness.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Critter weakness "not-found" is invalid'
        );
        new CritterWeakness('not-found');
    }

    /**
     * Test loading a weakness without a subname.
     */
    public function testLoadNoSubname(): void
    {
        $weakness = new CritterWeakness('uneducated');
        self::assertNotNull($weakness->description);
        self::assertSame('Uneducated', $weakness->name);
        self::assertSame('Uneducated', (string)$weakness);
        self::assertSame(401, $weakness->page);
        self::assertSame('core', $weakness->ruleset);
    }

    /**
     * Test loading a weakness with a subname.
     */
    public function testLoadWithSubname(): void
    {
        $weakness = new CritterWeakness('allergy', 'silver');
        self::assertSame('Allergy', $weakness->name);
        self::assertSame('silver', $weakness->subname);
        self::assertSame('Allergy - silver', (string)$weakness);
    }
}
