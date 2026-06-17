<?php

declare(strict_types=1);

namespace Modules\Stillfleet\Tests\Feature\Models;

use Modules\Stillfleet\Enums\TechStrata;
use Modules\Stillfleet\Models\Armor;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('stillfleet')]
#[Small]
final class ArmorTest extends TestCase
{
    public function testToString(): void
    {
        $armor = Armor::find('chainmail');
        self::assertSame('Chainmail', (string)$armor);
    }

    public function testTechStrata(): void
    {
        $armor = Armor::findOrFail('chainmail');
        self::assertSame(TechStrata::Clank, $armor->tech_strata);
    }
}
