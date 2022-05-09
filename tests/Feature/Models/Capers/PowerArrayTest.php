<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Capers;

use App\Models\Capers\Power;
use App\Models\Capers\PowerArray;

/**
 * Tests for the PowerArray class.
 * @group capers
 * @group models
 * @small
 */
final class PowerArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var PowerArray<Power>
     */
    protected PowerArray $powers;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->powers = new PowerArray();
    }

    /**
     * Test an empty PowerArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->powers);
    }

    /**
     * Test adding a skill to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->powers[] = new Power('acid-stream');
        self::assertNotEmpty($this->powers);
    }

    /**
     * Test that adding a non-skill to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->powers[] = new \StdClass();
    }

    /**
     * Test that adding a non-skill to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->powers->offsetSet(power: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->powers);
    }
}
