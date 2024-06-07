<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Boost;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;

/**
 * Tests for Boost model.
 * @group capers
 */
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
