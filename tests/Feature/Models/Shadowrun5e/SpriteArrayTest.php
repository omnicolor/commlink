<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\Sprite;
use App\Models\Shadowrun5e\SpriteArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
final class SpriteArrayTest extends TestCase
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->sprites);
    }

    /**
     * Test adding to the array.
     */
    public function testAdd(): void
    {
        $this->sprites[] = new Sprite('courier');
        self::assertNotEmpty($this->sprites);
    }

    /**
     * Test that adding the wrong type to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->sprites[] = new stdClass();
    }

    /**
     * Test that adding the wrong type to the array doesn't add the object.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->sprites->offsetSet(sprite: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->sprites);
    }
}
