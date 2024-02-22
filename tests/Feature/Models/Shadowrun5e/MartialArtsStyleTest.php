<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\MartialArtsStyle;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for MartialArtsStyle class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class MartialArtsStyleTest extends TestCase
{
    /**
     * Test trying to load an invalid MartialArtsStyle.
     * @test
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Martial Arts Style ID "foo" is invalid');
        new MartialArtsStyle('foo');
    }

    /**
     * Test loading style.
     * @test
     */
    public function testConstructor(): void
    {
        $style = new MartialArtsStyle('aikido');
        self::assertNotNull($style->description);
        self::assertEquals('aikido', $style->id);
        self::assertEquals('Aikido', $style->name);
        self::assertEquals(128, $style->page);
        self::assertEquals('run-and-gun', $style->ruleset);
        self::assertNotEmpty($style->allowedTechniques);
    }

    /**
     * Test the __toString() method.
     * @test
     */
    public function testToString(): void
    {
        $style = new MartialArtsStyle('Aikido');
        self::assertEquals('Aikido', (string)$style);
    }
}
