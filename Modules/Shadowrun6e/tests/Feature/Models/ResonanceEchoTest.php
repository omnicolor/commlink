<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\ResonanceEcho;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
class ResonanceEchoTest extends TestCase
{
    public function testToString(): void
    {
        $echo = ResonanceEcho::findOrFail('living-network');
        self::assertSame('Living Network', (string)$echo);
    }
}
