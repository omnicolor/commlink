<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Boost;
use Tests\TestCase;

/**
 * Tests for Boost model.
 * @group capers
 * @small
 */
final class BoostTest extends TestCase
{
    /**
     * Test creating a new Boost.
     * @test
     */
    public function testBoost(): void
    {
        $boost = new Boost('id', 'Description', 'Name');
        self::assertSame('Name', (string)$boost);
        self::assertSame('id', $boost->id);
        self::assertSame('Description', $boost->description);
    }
}
