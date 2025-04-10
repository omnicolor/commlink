<?php

declare(strict_types=1);

namespace Modules\Transformers\Tests\Feature\Models;

use Modules\Transformers\Models\Weapon;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('transformers')]
#[Small]
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
        // @phpstan-ignore property.notFound
        self::assertNull($weapon->invalid_property);
    }
}
