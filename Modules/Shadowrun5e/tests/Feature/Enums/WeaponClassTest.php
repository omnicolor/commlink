<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Enums;

use Modules\Shadowrun5e\Enums\WeaponClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class WeaponClassTest extends TestCase
{
    public function testName(): void
    {
        self::assertSame('Taser', WeaponClass::Taser->name());
    }
}
