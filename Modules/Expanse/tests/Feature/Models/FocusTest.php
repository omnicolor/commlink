<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\Focus;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

use function count;

#[Group('expanse')]
#[Small]
final class FocusTest extends TestCase
{
    public function testLoadInvalidFocus(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Focus ID "q" is invalid');
        new Focus('q');
    }

    public function testLoadValidFocus(): void
    {
        $focus = new Focus('crafting');
        self::assertSame('dexterity', $focus->attribute);
        self::assertSame('crafting', $focus->id);
        self::assertSame('Crafting', $focus->name);
        self::assertSame(47, $focus->page);
    }

    public function testToString(): void
    {
        $focus = new Focus('crafting');
        self::assertSame('Crafting', (string)$focus);
    }

    /**
     * Test not setting the level for the focus.
     */
    public function testDefaultLevel(): void
    {
        $focus = new Focus('crafting');
        self::assertSame(1, $focus->level);
    }

    /**
     * Test setting the level for the focus.
     */
    public function testSetLevel(): void
    {
        $focus = new Focus('crafting', 2);
        self::assertSame(2, $focus->level);
    }

    public function testAll(): void
    {
        self::assertGreaterThan(0, count(Focus::all()));
    }
}
