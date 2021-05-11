<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\KarmaLogEntry;

/**
 * Tests for KarmaLogEntry class.
 * @covers \App\Models\Shadowrun5E\KarmaLogEntry
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class KarmaLogEntryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test the constructor with null dates.
     * @test
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
     * @test
     */
    public function testConstructorDates(): void
    {
        $realDate = new \DateTimeImmutable('2020-03-15');
        $gameDate = new \DateTimeImmutable('2080-04-01');
        $entry = new KarmaLogEntry('Foo', 42, $realDate, $gameDate);
        self::assertSame('Foo', $entry->description);
        self::assertSame(42, $entry->karma);
        self::assertEquals(
            new \DateTimeImmutable('2020-03-15'),
            $entry->realDate
        );
        self::assertEquals(
            new \DateTimeImmutable('2080-04-01'),
            $entry->gameDate
        );
    }
}
