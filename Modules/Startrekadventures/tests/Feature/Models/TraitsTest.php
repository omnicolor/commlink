<?php

declare(strict_types=1);

namespace Modules\StartrekAdventures\Tests\Feature\Models;

use Modules\Startrekadventures\Models\Traits;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('startrekadventures')]
#[Small]
final class TraitsTest extends TestCase
{
    public function testFindNotFound(): void
    {
        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Trait ID "not-found" is invalid');
        new Traits('not-found');
    }

    public function testFind(): void
    {
        $trait = new Traits('human');
        self::assertSame(107, $trait->page);
        self::assertSame('core', $trait->ruleset);
        self::assertSame('Human', (string)$trait);
    }
}
