<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Quality;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class QualityTest extends TestCase
{
    /**
     * Test loading an invalid quality.
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
     */
    public function testFindByName(): void
    {
        $qualities = Quality::findByName('Focused Concentration');
        self::assertCount(3, $qualities);
    }
}
