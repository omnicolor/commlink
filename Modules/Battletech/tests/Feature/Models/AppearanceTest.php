<?php

declare(strict_types=1);

namespace Modules\Battletech\Tests\Feature\Models;

use Modules\Battletech\Models\Appearance;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('battletech')]
#[Small]
final class AppearanceTest extends TestCase
{
    public function testMakeWithNull(): void
    {
        $appearance = Appearance::make(null);
        self::assertEquals(new Appearance(), $appearance);
    }

    public function testMakeWithArray(): void
    {
        $appearance = Appearance::make([
            'eyes' => 'blue',
            'hair' => 'red',
        ]);

        self::assertNull($appearance->extra);
        self::assertSame('blue', $appearance->eyes);
        self::assertSame('red', $appearance->hair);
    }

    public function testToArrayEmpty(): void
    {
        $appearance = new Appearance();
        self::assertSame([], $appearance->toArray());
    }

    public function testToArray(): void
    {
        $appearance = Appearance::make([
            'height' => 187,
            'weight' => 150,
        ]);
        $appearance->extra = 'Extra info';

        self::assertSame(
            [
                'extra' => 'Extra info',
                'height' => 187,
                'weight' => 150,
            ],
            $appearance->toArray(),
        );
    }
}
