<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Expanse;

use App\Models\Expanse\Background;
use App\Models\Expanse\Focus;
use App\Models\Expanse\Talent;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class BackgroundTest extends TestCase
{
    public function testLoadInvalidBackground(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Background ID "q" is invalid');
        new Background('q');
    }

    public function testLoadValidBackground(): void
    {
        $background = new Background('trade');
        self::assertSame('dexterity', $background->ability);
        self::assertCount(11, $background->benefits);
        self::assertCount(2, $background->focuses);
        self::assertSame('trade', $background->id);
        self::assertSame('Trade', $background->name);
        self::assertSame(33, $background->page);
        self::assertCount(2, $background->talents);
    }

    public function testToString(): void
    {
        $background = new Background('trade');
        self::assertSame('Trade', (string)$background);
    }

    public function testGetFocuses(): void
    {
        $focuses = (new Background('trade'))->getFocuses();
        /** @var Focus */
        $focus = $focuses[0];
        self::assertSame('Crafting', $focus->name);
    }

    public function testGetTalents(): void
    {
        $talents = (new Background('trade'))->getTalents();
        /** @var Talent */
        $talent = $talents[0];
        self::assertSame('Maker', $talent->name);
    }
}
