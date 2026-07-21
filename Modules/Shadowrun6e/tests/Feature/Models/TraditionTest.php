<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Tradition;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun64')]
#[Small]
final class TraditionTest extends TestCase
{
    public function testAttributes(): void
    {
        $tradition = Tradition::findOrFail('hermetic');
        self::assertSame(['logic', 'willpower'], $tradition->drain_attributes);
    }

    public function testToString(): void
    {
        $tradition = Tradition::findOrFail('hermetic');
        self::assertSame('Hermetic', (string)$tradition);
    }
}
