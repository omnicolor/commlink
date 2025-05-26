<?php

declare(strict_types=1);

namespace Modules\Root\Tests\Feature\Models;

use Modules\Root\Models\Move;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('root')]
#[Small]
final class MoveTest extends TestCase
{
    public function testLoadWithoutEffects(): void
    {
        $move = Move::findOrFail('carry-a-big-stick');
        self::assertSame('Carry a Big Stick', (string)$move);
        self::assertNull($move->effects);
    }

    public function testLoadWithEffects(): void
    {
        $move = Move::findOrFail('brute');
        self::assertSame('Brute', $move->name);
        self::assertEquals(
            (object)[
                'might' => 1,
            ],
            $move->effects,
        );
        self::assertSame('Take +1 to Might (max +3).', $move->description);
    }

    public function testScopeMove(): void
    {
        $moves = Move::move()->get();
        foreach ($moves as $move) {
            self::assertFalse($move->weapon_move);
        }
    }

    public function testScopeWeapon(): void
    {
        $moves = Move::weapon()->get();
        foreach ($moves as $move) {
            self::assertTrue($move->weapon_move);
        }
    }
}
