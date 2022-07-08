<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun6e;

use App\Models\Shadowrun6e\Quality;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for Shadowrun 6E qualities.
 * @group models
 * @group shadowrun
 * @group shadowrun6e
 * @small
 */
final class QualityTest extends TestCase
{
    /**
     * Test loading an invalid quality.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Shadowrun 6E quality ID "not-found" is invalid'
        );
        new Quality('not-found');
    }

    /**
     * Test loading a valid quality.
     * @test
     */
    public function testLoad(): void
    {
        $quality = new Quality('ambidextrous');

        self::assertSame('Ambidextrous', (string)$quality);
        self::assertSame('ambidextrous', $quality->id);
        self::assertSame(4, $quality->karma_cost);
        self::assertEmpty($quality->effects);
        self::assertStringContainsString('off-hand', $quality->description);
        self::assertSame('core', $quality->ruleset);
        self::assertSame(70, $quality->page);
        self::assertNull($quality->level);
    }

    /**
     * Test failing to find a quality by its name.
     * @test
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Unable to find Shadowrun 6E quality "Not Found"'
        );
        Quality::findByName('Not Found');
    }

    /**
     * Test finding a quality by name.
     * @test
     */
    public function testFindByName(): void
    {
        $qualities = Quality::findByName('Focused Concentration');
        self::assertCount(3, $qualities);
    }
}
