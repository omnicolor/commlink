<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Ritual;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class RitualTest extends TestCase
{
    public function testRitual(): void
    {
        $ritual = Ritual::findOrFail('circle-of-healing');
        self::assertSame('Circle Of Healing', (string)$ritual);
    }
}
