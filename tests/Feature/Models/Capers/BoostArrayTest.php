<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Boost;
use App\Models\Capers\BoostArray;

/**
 * Tests for the BoostArray class.
 * @group capers
 * @group models
 * @small
 */
final class BoostArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var BoostArray<Boost>
     */
    protected BoostArray $boosts;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->boosts = new BoostArray();
    }

    /**
     * Test an empty BoostArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->boosts);
    }

    /**
     * Test adding a skill to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->boosts[] = new Boost('foo', 'Foo', 'Foo description');
        self::assertNotEmpty($this->boosts);
    }

    /**
     * Test that adding a non-skill to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->boosts[] = new \StdClass();
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->boosts->offsetSet(boost: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->boosts);
    }
}
