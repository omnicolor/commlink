<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\MentorSpirit;

/**
 * Unit tests for MentorSpirit class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class MentorSpiritTest extends \Tests\TestCase
{
    /**
     * Test trying to load an invalid MentorSpirit.
     * @test
     */
    public function testLoadInvalid(): void
    {
        MentorSpirit::$spirits = null;
        self::expectException(\RuntimeException::class);
        self::expectExceptionMessage('Mentor spirit ID "foo" is invalid');
        new MentorSpirit('foo');
    }

    /**
     * Test the constructor.
     * @test
     */
    public function testConstructor(): void
    {
        $mentor = new MentorSpirit('goddess');
        self::assertNotNull($mentor->description);
        self::assertEmpty($mentor->effects);
        self::assertSame('goddess', $mentor->id);
        self::assertSame('Goddess', $mentor->name);
        self::assertSame(129, $mentor->page);
        self::assertSame('book-of-the-lost', $mentor->ruleset);
    }

    /**
     * Test the __toString() method.
     * @test
     */
    public function testToString(): void
    {
        $mentor = new MentorSpirit('goddess');
        self::assertSame('Goddess', (string)$mentor);
    }
}
