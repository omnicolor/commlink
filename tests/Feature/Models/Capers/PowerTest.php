<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Power;
use RuntimeException;
use Tests\TestCase;

/**
 * Tests for Capers powers.
 * @group capers
 * @small
 */
final class PowerTest extends TestCase
{
    /**
     * Test creating an invalid power.
     */
    public function testLoadInvalidPower(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Power ID "invalid" is invalid');
        new Power('invalid');
    }

    /**
     * Test creating a power with no boosts and no additional ranks.
     */
    public function testLoadPower(): void
    {
        $power = new Power('acid-stream');
        self::assertSame('Acid stream', (string)$power);
        self::assertSame('Power Check vs. target’s Body', $power->activation);
        self::assertNotEmpty($power->availableBoosts);
        self::assertEmpty($power->boosts);
        self::assertSame(
            'You create a stream of corrosive acid.',
            $power->description
        );
        self::assertSame('Instantaneous', $power->duration);
        self::assertSame(5, $power->maxRank);
        self::assertSame('Acid stream', $power->name);
        self::assertSame('30’', $power->range);
        self::assertSame(1, $power->rank);
        self::assertSame('Anything', $power->target);
        self::assertSame(Power::TYPE_MINOR, $power->type);
    }

    /**
     * Test creating a power with a chosen boost.
     */
    public function testLoadPowerWithBoost(): void
    {
        $power = new Power('acid-stream', 1, ['acrid-cloud-boost']);
        self::assertNotEmpty($power->boosts);
        self::assertSame('Acrid cloud boost', (string)$power->boosts[0]);
    }

    /**
     * Test loading all powers.
     */
    public function testLoadAll(): void
    {
        $hasMajor = $hasMinor = false;
        $powers = Power::all();
        self::assertNotEmpty($powers);
        foreach ($powers as $power) {
            if (Power::TYPE_MINOR === $power->type) {
                $hasMinor = true;
            } elseif (Power::TYPE_MAJOR === $power->type) {
                $hasMajor = true;
            }

            if ($hasMajor && $hasMinor) {
                return;
            }
        }
        self::fail('Collection of all Powers missing either major or minor powers');
    }

    /**
     * Test loading major powers only.
     */
    public function testMajor(): void
    {
        $powers = Power::major();
        self::assertNotEmpty($powers);
        foreach ($powers as $power) {
            if (Power::TYPE_MINOR === $power->type) {
                self::fail('Minor power included in major power list');
            }
            self::assertSame(Power::TYPE_MAJOR, $power->type);
        }
    }

    /**
     * Test loading minor powers only.
     */
    public function testMinor(): void
    {
        $powers = Power::minor();
        self::assertNotEmpty($powers);
        foreach ($powers as $power) {
            if (Power::TYPE_MAJOR === $power->type) {
                self::fail('Major power included in minor power list');
            }
            self::assertSame(Power::TYPE_MINOR, $power->type);
        }
    }
}
