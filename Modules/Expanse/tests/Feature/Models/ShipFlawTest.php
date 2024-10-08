<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\ShipFlaw;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class ShipFlawTest extends TestCase
{
    public function testLoadNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Expanse ship flaw "not-found" is invalid'
        );
        new ShipFlaw('not-found');
    }

    public function testLoad(): void
    {
        $flaw = new ShipFlaw('bad-juice');
        self::assertSame('Bad juice', (string)$flaw);
        self::assertEmpty($flaw->effects);
        self::assertSame(123, $flaw->page);
        self::assertSame('core', $flaw->ruleset);
    }

    /**
     * Test loading all flaws.
     */
    public function testAll(): void
    {
        self::assertCount(2, ShipFlaw::all());
    }
}
