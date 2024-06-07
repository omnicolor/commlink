<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5e;

use App\Models\Shadowrun5e\MartialArtsTechnique;
use App\Models\Shadowrun5e\MartialArtsTechniqueArray;
use PHPUnit\Framework\Attributes\Small;
use Tests\TestCase;
use TypeError;
use stdClass;

/**
 * Tests for the MartialArtsTechniqueArray class.
 * @group shadowrun
 * @group shadowrun5e
 */
#[Small]
final class MartialArtsTechniqueArrayTest extends TestCase
{
    /**
     * Subject under test.
     * @var MartialArtsTechniqueArray<MartialArtsTechnique>
     */
    protected MartialArtsTechniqueArray $techniques;

    /**
     * Set up a clean subject.
     */
    public function setUp(): void
    {
        parent::setUp();
        $this->techniques = new MartialArtsTechniqueArray();
    }

    /**
     * Test an empty MartialArtsTechniqueArray.
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->techniques);
    }

    /**
     * Test adding a technique to the array.
     */
    public function testAdd(): void
    {
        $this->techniques[] = new MartialArtsTechnique('constrictors-crush');
        self::assertNotEmpty($this->techniques);
    }

    /**
     * Test that adding a non-technique to the array throws an exception.
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(TypeError::class);
        // @phpstan-ignore-next-line
        $this->techniques[] = new stdClass();
    }

    /**
     * Test that adding a non-technique to the array doesn't add it.
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->techniques->offsetSet(technique: new stdClass());
        } catch (TypeError) {
            // Ignored
        }
        self::assertEmpty($this->techniques);
    }
}
