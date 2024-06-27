<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Transformers;

use App\Models\Transformers\Weapon;
use App\Models\Transformers\WeaponArray;
use Tests\TestCase;
use TypeError;

/**
 * @group models
 * @group transformers
 * @small
 */
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
