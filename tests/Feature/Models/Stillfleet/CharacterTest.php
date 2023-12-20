<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Stillfleet;

use App\Models\Stillfleet\Character;
use App\Models\Stillfleet\Role;
use LogicException;
use RuntimeException;
use Tests\TestCase;

/**
 * @group stillfleet
 * @small
 */
class CharacterTest extends TestCase
{
    public function testConvertNotEnoughHealth(): void
    {
        $character = new Character([
            'combat' => 6,
            'health_current' => 2,
            'movement' => 10,
            'reason' => 4,
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Not enough health to convert');
        $character->convert();
    }

    public function testConvert(): void
    {
        $character = new Character([
            'combat' => 6,
            'movement' => 10,
            'reason' => 4,
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);

        self::assertSame(14, $character->grit);
        self::assertSame(14, $character->grit_current);
        self::assertSame(16, $character->health);
        self::assertSame(16, $character->health_current);

        $character->grit_current = 9;
        $character->health_current = 10;
        $character->convert();

        self::assertSame(10, $character->grit_current);
        self::assertSame(7, $character->health_current);
    }

    public function testGrit(): void
    {
        // Banshee's grit is movement + reason.
        $character = new Character([
            'movement' => 10,
            'reason' => 4,
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);
        self::assertSame(14, $character->grit);
        $character->movement = 12;
        self::assertSame(16, $character->grit);
        $character->reason = 6;
        self::assertSame(18, $character->grit);
    }

    public function testWriteGrit(): void
    {
        $character = new Character([
            'combat' => 6,
            'movement' => 10,
            'reason' => 4,
            'roles' => [['id' => 'banshee', 'level' => 1]],
        ]);

        self::expectException(LogicException::class);
        // @phpstan-ignore-next-line
        $character->grit = 10;
    }

    public function testHealth(): void
    {
        $character = new Character(['combat' => 4, 'movement' => 4]);
        self::assertSame(8, $character->health);
        self::assertSame(8, $character->health_current);
        $character->combat = 6;
        self::assertSame(10, $character->health);
        $character->movement = 8;
        self::assertSame(14, $character->health);
    }

    public function testRoleInvalid(): void
    {
        $character = new Character([
            'roles' => [
                [
                    'id' => 'invalid',
                    'level' => 1,
                ],
            ],
        ]);
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Role ID "invalid" is invalid');
        // @phpstan-ignore-next-line
        $character->roles;
    }

    public function testRoles(): void
    {
        $character = new Character([
            'roles' => [
                [
                    'id' => 'banshee',
                    'level' => 1,
                ],
            ],
        ]);

        self::assertInstanceOf(Role::class, $character->roles[0]);
    }
}