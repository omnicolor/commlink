<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Metamagic;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class MetamagicTest extends TestCase
{
    /**
     * Test trying to load an invalid Metamagic.
     */
    public function testLoadInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Metamagic ID "foo" is invalid');
        new Metamagic('foo');
    }

    /**
     * Test trying to load a valid Metamagic and convert it to a string.
     */
    public function testToString(): void
    {
        $magic = new Metamagic('centering');
        self::assertSame('Centering', (string)$magic);
    }

    /**
     * Test trying to find a metamagic by name, not found.
     */
    public function testFindByNameNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Metamagic "not found" was not found');
        Metamagic::findByName('Not Found');
    }

    /**
     * Test trying to find a metamagic by name.
     */
    public function testFindByName(): void
    {
        $meta = Metamagic::findByName('Centering');
        self::assertSame('centering', $meta->id);
    }
}
