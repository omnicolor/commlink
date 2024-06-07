<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\MentorSpirit;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

/**
 * Unit tests for MentorSpirit class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class MentorSpiritTest extends TestCase
{
    /**
     * Test trying to load an invalid MentorSpirit.
     */
    public function testLoadInvalid(): void
    {
        MentorSpirit::$spirits = null;
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Mentor spirit ID "foo" is invalid');
        new MentorSpirit('foo');
    }

    /**
     * Test the constructor.
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
     */
    public function testToString(): void
    {
        $mentor = new MentorSpirit('goddess');
        self::assertSame('Goddess', (string)$mentor);
    }

    /**
     * Test trying to find a mentor spirit by name with an invalid name.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage(
            'Mentor spirit name "invalid" was not found',
        );
        MentorSpirit::findByName('invalid');
    }

    /**
     * Test finding a mentor spirit by name.
     */
    public function testFindByName(): void
    {
        $spirit = MentorSpirit::findByName('bear');
        self::assertSame('Bear', $spirit->name);
    }
}
