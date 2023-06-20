<?php

declare(strict_types=1);

namespace Tests\Features\Models\Transformers;

use App\Models\Transformers\Weapon;
use Tests\TestCase;
use RuntimeException;

/**
 * @group models
 * @group transformers
 * @small
 */
final class WeaponTest extends TestCase
{
    public function testInvalid(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Weapon ID "invalid" is invalid');
        new Weapon('invalid');
    }

    public function testValid(): void
    {
        $weapon = new Weapon('buzzsaw');
        self::assertSame('Buzzsaw', (string)$weapon);
        self::assertSame('Minor', $weapon->class->name);
        self::assertSame(1, $weapon->cost);
        self::assertNull($weapon->invalid_property);
    }
}
