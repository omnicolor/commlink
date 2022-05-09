<?php

declare(strict_types=1);

namespace Tests\Feature\Models\Shadowrun5E;

use App\Models\Shadowrun5E\MartialArtsTechnique;
use App\Models\Shadowrun5E\MartialArtsTechniqueArray;

/**
 * Tests for the MartialArtsTechniqueArray class.
 * @group models
 * @group shadowrun
 * @group shadowrun5e
 * @small
 */
final class MartialArtsTechniqueArrayTest extends \Tests\TestCase
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
     * @test
     */
    public function testEmpty(): void
    {
        self::assertEmpty($this->techniques);
    }

    /**
     * Test adding a technique to the array.
     * @test
     */
    public function testAdd(): void
    {
        $this->techniques[] = new MartialArtsTechnique('constrictors-crush');
        self::assertNotEmpty($this->techniques);
    }

    /**
     * Test that adding a non-technique to the array throws an exception.
     * @test
     */
    public function testAddWrongTypeException(): void
    {
        self::expectException(\TypeError::class);
        // @phpstan-ignore-next-line
        $this->techniques[] = new \StdClass();
    }

    /**
     * Test that adding a non-technique to the array doesn't add it.
     * @test
     */
    public function testAddWrongTypeDoesntAdd(): void
    {
        try {
            // @phpstan-ignore-next-line
            $this->techniques->offsetSet(technique: new \StdClass());
        } catch (\TypeError $e) {
            // Ignored
        }
        self::assertEmpty($this->techniques);
    }
}
