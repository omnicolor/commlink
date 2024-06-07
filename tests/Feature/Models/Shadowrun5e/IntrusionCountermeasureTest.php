<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\IntrusionCountermeasure;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for ICE class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class IntrusionCountermeasureTest extends TestCase
{
    public function testNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Intrusion countermeasure ID "not-found" is invalid'
        );
        new IntrusionCountermeasure('not-found');
    }

    public function testLoadNoRatings(): void
    {
        $ice = new IntrusionCountermeasure('ACID');
        self::assertSame('acid', $ice->id);
        self::assertSame('Willpower + Firewall', $ice->defense);
        self::assertSame('Acid', $ice->name);
        self::assertSame(248, $ice->page);
        self::assertSame('core', $ice->ruleset);
        self::assertSame(0, $ice->initiative_base);
        self::assertSame(4, $ice->initiative_dice);

        self::assertSame(
            ['Willpower', 'Firewall'],
            $ice->getDefenseAttributes()
        );
        self::assertNull($ice->attack);
        self::assertNull($ice->data_processing);
        self::assertNull($ice->firewall);
        self::assertNull($ice->sleaze);
    }

    public function testHostRatingWithoutAttributes(): void
    {
        $ice = new IntrusionCountermeasure('acid');
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Host rating requires ASDF attributes to be set'
        );
        $ice->getHostRating();
    }

    public function testConditionMonitorWithoutAttribtes(): void
    {
        $ice = new IntrusionCountermeasure('acid');
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Host rating requires ASDF attributes to be set'
        );
        $ice->getConditionMonitor();
    }

    public function testIceWithRatings(): void
    {
        $ice = new IntrusionCountermeasure(
            id: 'blue-goo',
            attack: 3,
            dataProcessing: 4,
            firewall: 5,
            sleaze: 6
        );

        self::assertSame('Blue goo', (string)$ice);

        self::assertSame(3, $ice->attack);
        self::assertSame(4, $ice->data_processing);
        self::assertSame(5, $ice->firewall);
        self::assertSame(6, $ice->sleaze);

        self::assertSame(3, $ice->getHostRating());
        self::assertSame(10, $ice->getConditionMonitor());

        self::assertSame(
            ['Logic', 'Firewall'],
            $ice->getDefenseAttributes()
        );
    }
}
