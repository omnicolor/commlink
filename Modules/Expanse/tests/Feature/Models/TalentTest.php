<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\Talent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class TalentTest extends TestCase
{
    /**
     * Test trying to load an invalid talent.
     */
    public function testLoadInvalidTalent(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Talent ID "q" is invalid');
        new Talent('q');
    }

    /**
     * Test trying to load a valid talent.
     */
    public function testLoadValidTalent(): void
    {
        $talent = new Talent('fringer');
        self::assertSame('fringer', $talent->id);
        self::assertSame('Fringer', $talent->name);
    }

    /**
     * Test casting a talent to a string.
     */
    public function testToString(): void
    {
        $talent = new Talent('Fringer');
        self::assertSame('Fringer', (string)$talent);
    }

    /**
     * Test loading a talent without setting the level defaults to Novice.
     */
    public function testDefaultLevel(): void
    {
        $talent = new Talent('fringer');
        self::assertSame(Talent::NOVICE, $talent->level);
    }

    /**
     * Test setting the level to an invalid level.
     */
    public function testSetLevelInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Talent level outside allowed values');
        (new Talent('fringer'))->setLevel(99);
    }
}
