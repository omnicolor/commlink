<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Models;

use InvalidArgumentException;
use LogicException;
use Modules\Battletech\Enums\ExperienceItemType;
use Modules\Battletech\Models\ExperienceItem;
use Modules\Battletech\Models\ExperienceLog;
use PHPUnit\Framework\TestCase;

final class ExperienceLogTest extends TestCase
{
    public function testEmptyTotal(): void
    {
        $log = ExperienceLog::empty();
        self::assertSame(0, $log->total());
    }

    public function testNonEmptyTotal(): void
    {
        $items = [];
        $items[] = new ExperienceItem(100, ExperienceItemType::Skill, 'Painting');
        $items[] = new ExperienceItem(400, ExperienceItemType::Attribute, 'STR');
        $log = new ExperienceLog(...$items);
        self::assertSame(500, $log->total());
    }

    public function testFilteredTotal(): void
    {
        $items = [];
        $items[] = new ExperienceItem(200, ExperienceItemType::Skill, 'Painting');
        $items[] = new ExperienceItem(300, ExperienceItemType::Attribute, 'STR');
        $log = new ExperienceLog(...$items);
        self::assertSame(300, $log->total(ExperienceItemType::Attribute));
    }

    public function testAppendWrongType(): void
    {
        $log = ExperienceLog::empty();
        self::expectException(InvalidArgumentException::class);
        $log[] = 'test';
    }

    public function testAppendAndCount(): void
    {
        $log = ExperienceLog::empty();
        self::assertCount(0, $log);
        self::assertNull($log[0]);
        $log[] = $item = new ExperienceItem(
            100,
            ExperienceItemType::Attribute,
            'test',
        );
        self::assertCount(1, $log);
        self::assertSame($item, $log[0]);
    }

    public function testIsset(): void
    {
        $log = new ExperienceLog(
            new ExperienceItem(200, ExperienceItemType::Skill, 'foo'),
        );
        self::assertTrue(isset($log[0]));
        self::assertFalse(isset($log[1]));
    }

    public function testUnset(): void
    {
        $log = new ExperienceLog(
            new ExperienceItem(200, ExperienceItemType::Skill, 'foo'),
        );
        self::expectException(LogicException::class);
        unset($log[0]);
    }

    public function testSetDirectly(): void
    {
        $log = ExperienceLog::empty();
        self::expectException(LogicException::class);
        $log[1] = new ExperienceItem(100, ExperienceItemType::Skill, 'foo');
    }

    public function testIterate(): void
    {
        $log = new ExperienceLog(
            new ExperienceItem(200, ExperienceItemType::Skill, 'foo'),
            new ExperienceItem(400, ExperienceItemType::Attribute, 'foo'),
            new ExperienceItem(100, ExperienceItemType::Skill, 'foo'),
        );
        $total = 0;
        foreach ($log as $item) {
            self::assertInstanceOf(ExperienceItem::class, $item);
            $total += $item->amount;
        }
        self::assertSame(700, $total);
    }

    public function testTotalForIndividaulItem(): void
    {
        $log = new ExperienceLog(
            new ExperienceItem(200, ExperienceItemType::Skill, 'foo'),
            new ExperienceItem(400, ExperienceItemType::Attribute, 'foo'),
            new ExperienceItem(100, ExperienceItemType::Skill, 'bar'),
            new ExperienceItem(100, ExperienceItemType::Skill, 'foo'),
        );
        self::assertSame(300, $log->total(ExperienceItemType::Skill, 'foo'));
        self::assertSame(400, $log->total(ExperienceItemType::Attribute, 'foo'));
    }
}
