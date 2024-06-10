<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Dnd5e;

use App\Models\Dnd5e\DamageType;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Small]
final class DamageTypeTest extends TestCase
{
    public function testDescription(): void
    {
        $damage = DamageType::from('bludgeoning');
        self::assertStringStartsWith('Blunt force', $damage->description());
    }
}
