<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\Race;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun5e')]
#[Small]
final class RaceTest extends TestCase
{
    public function testToString(): void
    {
        $race = Race::findOrFail('human');
        self::assertSame('Human', (string)$race);
    }

    public function testGetSpecialPointsForInvalidPriority(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Invalid priority');
        (Race::findOrFail('human'))->getSpecialPointsForPriority('Z');
    }

    public function testGetSpecialPointsForPriority(): void
    {
        $race = Race::findOrFail('human');
        self::assertSame(9, $race->getSpecialPointsForPriority('A'));
        self::assertSame(1, $race->getSpecialPointsForPriority('E'));
    }
}
