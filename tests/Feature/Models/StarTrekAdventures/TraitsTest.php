<?php

declare(strict_types=1);

namespace Tests\Feature\Models\StarTrekAdventures;

use App\Models\StarTrekAdventures\Traits;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use RuntimeException;
use Tests\TestCase;

#[Group('star-trek-adventures')]
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
