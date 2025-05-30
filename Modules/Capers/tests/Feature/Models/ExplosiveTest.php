<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Models;

use Modules\Capers\Models\Explosive;
use Tests\TestCase;

final class ExplosiveTest extends TestCase
{
    public function testToString(): void
    {
        $explosive = new Explosive('detonation-wire', 1);
        self::assertSame('Detonation wire (50’)', (string)$explosive);
    }
}
