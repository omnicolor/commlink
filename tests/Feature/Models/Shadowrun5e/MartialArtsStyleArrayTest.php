<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\MartialArtsStyle;
use App\Models\Shadowrun5e\MartialArtsStyleArray;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the MartialArtsStyleArray class.
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class MartialArtsStyleArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var MartialArtsStyleArray<MartialArtsStyle>
     */
    protected MartialArtsStyleArray $styles;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->styles = new MartialArtsStyleArray();
    }

    /**
     * Test an empty MartialArtsStyleArray.
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->styles);
    }

    /**
     * Test adding a style to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->styles[] = new MartialArtsStyle('aikido');
        self::assertNotEmpty($this->styles);
    }

    /**
     * Test that adding a non-style to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->styles[] = new stdClass();
    }

    /**
     * Test that adding a non-style to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->styles->offsetSet(style: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->styles);
    }
}
