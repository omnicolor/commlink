<?php

declare(strict_types=1);

namespace Tests\Feature\Notifications\Shadowrun5e;

use App\Notifications\Shadowrun5e\Damage;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Small]
final class DamageTest extends TestCase
{
    public function testConstruct(): void
    {
        $damage = [
            'stun' => 1,
            'physical' => 2,
            'overflow' => 3,
        ];
        $notification = new Damage(...$damage);
        self::assertSame(1, $notification->stun);
        self::assertSame(2, $notification->physical);
        self::assertSame(3, $notification->overflow);
    }

    public function testVia(): void
    {
        $notification = new Damage(3, 2, 1);
        self::assertSame(['broadcast'], $notification->via());
    }

    public function testToBroadcast(): void
    {
        $notification = new Damage(3, 2, 1);
        $message = $notification->toBroadcast();
        self::assertSame(3, $message->data['stun']);
        self::assertSame(2, $message->data['physical']);
        self::assertSame(1, $message->data['overflow']);
    }

    public function testToArray(): void
    {
        $damage = [
            'stun' => 1,
            'physical' => 2,
            'overflow' => 3,
        ];
        $notification = new Damage(...$damage);
        self::assertSame($damage, $notification->toArray());
    }
}
