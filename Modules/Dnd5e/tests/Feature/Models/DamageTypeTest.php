<?php

declare(strict_types=1);

namespace Modules\Dnd5e\Tests\Feature\Models;

use Modules\Dnd5e\Models\DamageType;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\TestCase;

#[Group('dnd5e')]
#[Small]
final class DamageTypeTest extends TestCase
{
    public function testDescription(): void
    {
        $damage = DamageType::from('bludgeoning');
        self::assertStringStartsWith('Blunt force', $damage->description());
    }
}
