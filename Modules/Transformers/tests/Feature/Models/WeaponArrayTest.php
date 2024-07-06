<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Models;

use Modules\Transformers\Models\Weapon;
use Modules\Transformers\Models\WeaponArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;

#[Group('transformers')]
#[Small]
final class WeaponArrayTest extends TestCase
{
    public function testStoreInvalidType(): void
    {
        $weapons = new WeaponArray();
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $weapons[] = 'test';
    }

    public function testStoreValidType(): void
    {
        $weapons = new WeaponArray();
        self::assertCount(0, $weapons);
        $weapons[] = new Weapon('buzzsaw');
        self::assertCount(1, $weapons);
    }
}
