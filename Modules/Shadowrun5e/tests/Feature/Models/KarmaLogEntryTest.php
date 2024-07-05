<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use DateTimeImmutable;
use Modules\Shadowrun5e\Models\KarmaLogEntry;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class KarmaLogEntryTest extends TestCase
{
    /**
     * Test the constructor with null dates.
     */
    public function testConstructorNullDates(): void
    {
        $entry = new KarmaLogEntry('Testing', -5);
        self::assertSame('Testing', $entry->description);
        self::assertSame(-5, $entry->karma);
        self::assertNull($entry->realDate);
        self::assertNull($entry->gameDate);
    }

    /**
     * Test the constructor with dates.
     */
    public function testConstructorDates(): void
    {
        $realDate = new DateTimeImmutable('2020-03-15');
        $gameDate = new DateTimeImmutable('2080-04-01');
        $entry = new KarmaLogEntry('Foo', 42, $realDate, $gameDate);
        self::assertSame('Foo', $entry->description);
        self::assertSame(42, $entry->karma);
        self::assertEquals(
            new DateTimeImmutable('2020-03-15'),
            $entry->realDate
        );
        self::assertEquals(
            new DateTimeImmutable('2080-04-01'),
            $entry->gameDate
        );
    }
}
