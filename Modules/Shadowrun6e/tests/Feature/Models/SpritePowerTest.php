<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\SpritePower;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun64')]
#[Small]
final class SpritePowerTest extends TestCase
{
    public function testToString(): void
    {
        $power = SpritePower::findOrFail('hash');
        self::assertSame('Hash', (string)$power);
    }
}
