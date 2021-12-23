<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\Sprite;
use App\Models\Shadowrun5E\SpriteArray;

/**
 * Tests for the SpriteArray.
 * @covers \App\Models\Shadowrun5E\SpriteArray
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class SpriteArrayTest extends \Tests\TestCase
{
    /**
     * Subject under test.
     * @var SpriteArray<Sprite>
     */
    protected SpriteArray $sprites;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->sprites = new SpriteArray();
    }

    /**
     * Test an empty array.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->sprites);
    }

    /**
     * Test adding to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->sprites[] = new Sprite('courier');
        self::assertNotEmpty($this->sprites);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->sprites[] = new \StdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->sprites->offsetSet(sprite: new \StdClass());
        } catch (\TypeError $ex) {
            // Ignored
        }
        self::assertEmpty($this->sprites);
    }
}
