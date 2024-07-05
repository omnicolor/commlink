<?php

declare(strict_types=1);

namespace Modules\Capers\Tests\Feature\Models;

use Modules\Capers\Models\Boost;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

#[Group('capers')]
#[Small]
final class BoostTest extends TestCase
{
    /**
     * Test creating a new Boost.
     */
    public function testBoost(): void
    {
        $boost = new Boost('id', 'Description', 'Name');
        self::assertSame('Name', (string)$boost);
        self::assertSame('id', $boost->id);
        self::assertSame('Description', $boost->description);
    }
}
