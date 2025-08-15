<?php

declare(strict_types=1);

namespace Modules\Shadowrun6e\Tests\Feature\Models;

use Modules\Shadowrun6e\Models\Metamagic;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('shadowrun')]
#[Group('shadowrun6e')]
#[Small]
final class MetamagicTest extends TestCase
{
    public function testToString(): void
    {
        $metamagic = Metamagic::findOrFail('centering');
        self::assertSame('Centering', (string)$metamagic);
    }
}
