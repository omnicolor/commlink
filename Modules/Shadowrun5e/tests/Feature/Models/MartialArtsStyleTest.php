<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\MartialArtsStyle;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class MartialArtsStyleTest extends TestCase
{
    /**
     * Test trying to load an invalid MartialArtsStyle.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Martial Arts Style ID "foo" is invalid');
        new MartialArtsStyle('foo');
    }

    /**
     * Test loading style.
     */
    public function testConstructor(): void
    {
        $style = new MartialArtsStyle('aikido');
        self::assertEquals('aikido', $style->id);
        self::assertEquals('Aikido', $style->name);
        self::assertEquals(128, $style->page);
        self::assertEquals('run-and-gun', $style->ruleset);
        self::assertNotEmpty($style->allowedTechniques);
    }

    /**
     * Test the __toString() method.
     */
    public function testToString(): void
    {
        $style = new MartialArtsStyle('Aikido');
        self::assertEquals('Aikido', (string)$style);
    }
}
