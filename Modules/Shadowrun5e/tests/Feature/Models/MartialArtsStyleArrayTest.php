<?php

declare(strict_types=1);

namespace Modules\Shadowrun5e\Tests\Feature\Models;

use Modules\Shadowrun5e\Models\MartialArtsStyle;
use Modules\Shadowrun5e\Models\MartialArtsStyleArray;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

#[Group('shadowrun')]
#[Group('shadowrun5e')]
#[Small]
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
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->styles);
    }

    /**
     * Test adding a style to the array.
     */
    public function testAdd(): void
    {
        $this->styles[] = new MartialArtsStyle('aikido');
        self::assertNotEmpty($this->styles);
    }

    /**
     * Test that adding a non-style to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->styles[] = new stdClass();
    }

    /**
     * Test that adding a non-style to the array doesn't add it.
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
