<?php

declare(strict_types=1);

namespace Modules\Expanse\Tests\Feature\Models;

use Modules\Expanse\Models\Condition;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('expanse')]
#[Small]
final class ConditionTest extends TestCase
{
    public function testLoadInvalidCondition(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Condition ID "q" is invalid');
        new Condition('q');
    }

    public function testLoadValidCondition(): void
    {
        $condition = new Condition('deafened');
        self::assertSame('deafened', $condition->id);
        self::assertSame('Deafened', $condition->name);
        self::assertSame(21, $condition->page);
    }

    public function testToString(): void
    {
        $condition = new Condition('deafened');
        self::assertSame('Deafened', (string)$condition);
    }
}
