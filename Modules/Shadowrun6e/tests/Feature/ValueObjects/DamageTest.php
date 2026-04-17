<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\ValueObjects;

use Modules\Shadowrun6e\Enums\DamageType;
use Modules\Shadowrun6e\ValueObjects\Damage;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class DamageTest extends TestCase
{
    public function testToString(): void
    {
        $damage = new Damage(DamageType::Physical, 10);
        self::assertSame('10P', (string)$damage);

        $damage = new Damage(DamageType::Stun, 6);
        self::assertSame('6S', (string)$damage);
    }
}
